<?php

use App\Actions\StartRecovery;
use App\Enums\ClassCourse;
use App\Enums\Gender;
use App\Livewire\Traits\InteractsWithToasts;
use App\Models\User;
use App\Notifications\VerificationCode;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Number;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Head\Facades\Head;
use libphonenumber\NumberParseException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Propaganistas\LaravelPhone\PhoneNumber;
use Propaganistas\LaravelPhone\Rules\Phone;

/**
 * Recovery wizard for accounts whose password is a sentinel. The member
 * proves they own the email address with a one-time code, then sets a
 * password (and, for imported accounts, completes their profile) before
 * being logged in.
 *
 * Everything that gates progress — which user, whether the code was verified,
 * the code itself — lives in the session, never in a public property, so the
 * client cannot skip the code step or read the member's details before it.
 */
new #[Layout('layouts::auth')] class extends Component
{
    use InteractsWithToasts;

    protected const int CODE_LIFETIME_MINUTES = 10;

    protected const int MAX_CODE_SENDS = 3;

    protected const int RESEND_DELAY_SECONDS = 60;

    protected const int MAX_VERIFY_ATTEMPTS = 5;

    protected const int THROTTLE_DECAY_SECONDS = 600;

    public string $code = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $class_course = '';

    public string $class_year = '';

    public string $gender = '';

    public string $birth_date = '';

    public string $phone = '';

    public function mount(): void
    {
        if (! $this->user?->requiresRecovery()) {
            Session::forget(StartRecovery::SESSION_KEY);

            $this->redirectRoute('login', navigate: true);

            return;
        }

        if ($this->verified) {
            $this->fillProfileFromUser();
        } elseif (! Session::has(StartRecovery::SESSION_KEY.'.code') && ! $this->codeRequestsExhausted) {
            $this->sendCode();
        }
    }

    public function verify(): void
    {
        $this->ensureRecoveryIsPending();

        $this->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $this->ensureVerificationIsNotRateLimited();

        if (! $this->codeIsValid()) {
            RateLimiter::hit($this->verifyThrottleKey(), self::THROTTLE_DECAY_SECONDS);

            throw ValidationException::withMessages([
                'code' => __('auth.recovery.status.invalid-code'),
            ]);
        }

        RateLimiter::clear($this->verifyThrottleKey());

        Session::put(StartRecovery::SESSION_KEY.'.verified', true);
        Session::forget([
            StartRecovery::SESSION_KEY.'.code',
            StartRecovery::SESSION_KEY.'.expires_at',
        ]);

        unset($this->verified);

        $this->fillProfileFromUser();
    }

    public function resendCode(): void
    {
        $this->ensureRecoveryIsPending();

        abort_if($this->resendAvailableIn > 0, 429);

        $this->sendCode();

        $this->toast(__('auth.recovery.verify.resent'), variant: 'success');
    }

    public function complete(): void
    {
        $this->ensureRecoveryIsPending();

        abort_unless($this->verified, 403);

        $user = $this->user;
        $migrating = $user->requiresMigration();

        $validated = $this->validate($this->completionRules($migrating));

        $user->forceFill([
            'password' => $validated['password'],
            'remember_token' => null,
        ]);

        if ($migrating) {
            $user
                ->fill(Arr::only($validated, $this->profileFields()))
                ->forceFill(['email_verified_at' => now()]);
        }

        $user->save();

        Session::forget(StartRecovery::SESSION_KEY);

        Auth::login($user);

        Session::regenerate();

        $this->redirect(app(LoginResponse::class)->toResponse(request())->getTargetUrl());
    }

    public function updatedPhone(): void
    {
        try {
            $this->phone = new PhoneNumber($this->phone, 'FR')->formatInternational();
        } catch (NumberParseException) {
        }
    }

    #[Computed]
    public function user(): ?User
    {
        return User::query()->find(Session::get(StartRecovery::SESSION_KEY.'.user_id'));
    }

    /**
     * The timestamp at which codes may be tried again after too many wrong
     * ones, or null when not locked out.
     */
    #[Computed]
    public function verifyAvailableAt(): ?int
    {
        if (! RateLimiter::tooManyAttempts($this->verifyThrottleKey(), self::MAX_VERIFY_ATTEMPTS)) {
            return null;
        }

        return now()->addSeconds(RateLimiter::availableIn($this->verifyThrottleKey()))->getTimestamp();
    }

    /**
     * Whether the cap on code requests has been reached; until it lifts, the
     * resend countdown explains why rather than inviting another request.
     */
    #[Computed]
    public function codeRequestsExhausted(): bool
    {
        return RateLimiter::tooManyAttempts($this->sendThrottleKey(), self::MAX_CODE_SENDS);
    }

    /**
     * The timestamp at which another code may be requested: a minute after
     * the last one, or later when the cap on code requests has been reached.
     */
    #[Computed]
    public function resendAvailableAt(): int
    {
        $availableAt = (int) Session::get(StartRecovery::SESSION_KEY.'.resend_available_at', 0);

        if ($this->codeRequestsExhausted) {
            $availableAt = max($availableAt, now()->addSeconds(RateLimiter::availableIn($this->sendThrottleKey()))->getTimestamp());
        }

        return $availableAt;
    }

    /**
     * Seconds before another code may be requested.
     */
    #[Computed]
    public function resendAvailableIn(): int
    {
        return max(0, $this->resendAvailableAt - now()->getTimestamp());
    }

    #[Computed]
    public function verified(): bool
    {
        return Session::get(StartRecovery::SESSION_KEY.'.verified') === true;
    }

    /**
     * Which flavour of recovery the member is going through.
     */
    #[Computed]
    public function state(): string
    {
        return $this->user->requiresMigration() ? 'migration' : 'password-reset';
    }

    /**
     * Imported accounts may be missing their class; only ask for it then.
     */
    #[Computed]
    public function mustSetClass(): bool
    {
        return is_null($this->user->class_course) || is_null($this->user->class_year);
    }

    protected function ensureRecoveryIsPending(): void
    {
        abort_unless($this->user?->requiresRecovery(), 403);
    }

    protected function ensureVerificationIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->verifyThrottleKey(), self::MAX_VERIFY_ATTEMPTS)) {
            return;
        }

        throw ValidationException::withMessages([
            'code' => __('auth.recovery.status.too-many-attempts', [
                'countdown' => Number::duration(RateLimiter::availableIn($this->verifyThrottleKey())),
            ]),
        ]);
    }

    protected function sendCode(): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Session::put(StartRecovery::SESSION_KEY.'.code', $code);
        Session::put(StartRecovery::SESSION_KEY.'.expires_at', now()->addMinutes(self::CODE_LIFETIME_MINUTES)->getTimestamp());
        Session::put(StartRecovery::SESSION_KEY.'.resend_available_at', now()->addSeconds(self::RESEND_DELAY_SECONDS)->getTimestamp());

        unset($this->codeRequestsExhausted, $this->resendAvailableAt, $this->resendAvailableIn);

        RateLimiter::hit($this->sendThrottleKey(), self::THROTTLE_DECAY_SECONDS);

        $this->user->notify(new VerificationCode($code, self::CODE_LIFETIME_MINUTES));
    }

    protected function codeIsValid(): bool
    {
        $expected = Session::get(StartRecovery::SESSION_KEY.'.code');
        $expiresAt = Session::get(StartRecovery::SESSION_KEY.'.expires_at', 0);

        return is_string($expected)
            && hash_equals($expected, $this->code)
            && now()->getTimestamp() <= $expiresAt;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function completionRules(bool $migrating): array
    {
        $rules = [
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];

        if (! $migrating) {
            return $rules;
        }

        if ($this->mustSetClass) {
            $rules += [
                'class_course' => ['required', Rule::enum(ClassCourse::class)],
                'class_year' => ['required', Rule::date()->format('Y')],
            ];
        }

        return $rules + [
            'gender' => ['required', Rule::enum(Gender::class)],
            'birth_date' => ['required', Rule::date()->format('Y-m-d')->after('150 years ago')->beforeOrEqual('15 years ago')],
            'phone' => ['required', (new Phone)->international()->country('FR')],
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function profileFields(): array
    {
        return [
            ...($this->mustSetClass ? ['class_course', 'class_year'] : []),
            'gender',
            'birth_date',
            'phone',
        ];
    }

    /**
     * Prefill the profile form from what the import carried over. Only called
     * once the code is verified, so the values never reach an unverified client.
     */
    protected function fillProfileFromUser(): void
    {
        $user = $this->user;

        if (! $user->requiresMigration()) {
            return;
        }

        $this->class_course = $user->class_course?->value ?? '';
        $this->class_year = (string) ($user->class_year ?? '');
        $this->gender = $user->gender ?? '';
        $this->birth_date = $user->birth_date?->format('Y-m-d') ?? '';
        $this->phone = $user->phone?->formatInternational() ?? '';
    }

    protected function verifyThrottleKey(): string
    {
        return 'recovery:verify:'.$this->user->getKey();
    }

    protected function sendThrottleKey(): string
    {
        return 'recovery:send:'.$this->user->getKey();
    }

    public function rendering(): void
    {
        Head::title(__('auth.recovery.title'));
    }
};
?>

<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('auth.recovery.heading.'.$this->state)"
        :description="$this->verified
            ? __('auth.recovery.complete.description.'.$this->state, ['name' => $this->user->first_name])
            : __('auth.recovery.verify.description')"
    />

    @if ($this->state === 'password-reset' && ! $this->verified)
        <flux:callout
            variant="warning"
            icon="shield-exclamation"
            :heading="__('auth.recovery.callouts.incident.heading')"
        >
            <flux:callout.text>{{ __('auth.recovery.callouts.incident.text') }}</flux:callout.text>

            <flux:callout.text>{{ __('auth.recovery.callouts.incident.cta') }}</flux:callout.text>

            <flux:callout.text>
                {{ __('auth.recovery.callouts.incident.questions') }}
                <flux:callout.link href="mailto:bureau@agepac.org">
                    {{ __('auth.recovery.callouts.incident.contact') }}</flux:callout.link>
            </flux:callout.text>
        </flux:callout>
    @endif

    @if (! $this->verified)
        <form wire:submit="verify" class="flex flex-col items-center gap-6">
            <flux:otp
                wire:model="code"
                submit="auto"
                :label="__('auth.recovery.verify.code-label')"
                :error:name="$this->verifyAvailableAt ? null : 'code'"
            >
                <flux:otp.input autofocus />
                <flux:otp.input />
                <flux:otp.input />
                <flux:otp.input />
                <flux:otp.input />
                <flux:otp.input />
            </flux:otp>

            @if ($this->verifyAvailableAt)
                <x-countdown
                    wire:key="verify-{{ $this->verifyAvailableAt }}"
                    class="-mt-3 block text-sm font-medium text-red-500 dark:text-red-400"
                    x-show="remaining > 0"
                    :until="$this->verifyAvailableAt"
                >
                    {{ __('auth.recovery.status.too-many-attempts') }}
                </x-countdown>
            @endif

            <flux:button
                type="submit"
                variant="primary"
                class="w-full"
            >{{ __('auth.recovery.verify.action') }}</flux:button>
        </form>

        <flux:subheading class="text-center text-zinc-600 dark:text-zinc-400">
            <x-countdown wire:key="resend-{{ $this->resendAvailableAt }}" :until="$this->resendAvailableAt">
                {{ __($this->codeRequestsExhausted ? 'auth.recovery.status.too-many-codes' : 'auth.recovery.verify.resend-wait') }}

                <x-slot:done>
                    {{ __('auth.recovery.verify.resend-prompt') }}
                    <flux:link
                        class="cursor-pointer"
                        wire:click="resendCode"
                    >{{ __('auth.recovery.verify.resend-link') }}</flux:link>
                </x-slot:done>
            </x-countdown>
        </flux:subheading>
    @else
        <form wire:submit="complete" class="flex flex-col gap-6">
            @if ($this->state === 'migration')
                @if ($this->mustSetClass)
                    <!-- Class Course -->
                    <flux:select
                        wire:model="class_course"
                        :label="__('fields.class-course.label')"
                        required
                        :placeholder="__('fields.class-course.placeholder')"
                    >
                        @foreach (ClassCourse::cases() as $course)
                            <flux:select.option :value="$course->value" :label="$course->getLabel()" />
                        @endforeach
                    </flux:select>

                    <!-- Class Year -->
                    <flux:input
                        wire:model="class_year"
                        :label="__('fields.class-year.label')"
                        type="text"
                        required
                        placeholder="YYYY"
                    />
                @endif

                <!-- Gender -->
                <flux:select
                    wire:model="gender"
                    :label="__('fields.gender.label')"
                    required
                    :placeholder="__('fields.gender.placeholder')"
                >
                    @foreach (Gender::cases() as $genderOption)
                        <flux:select.option :value="$genderOption->value" :label="$genderOption->getLabel()" />
                    @endforeach
                </flux:select>

                <!-- Birth Date -->
                <flux:date-picker wire:model="birth_date" :label="__('fields.birth-date.label')" required>
                    <x-slot name="trigger">
                        <flux:date-picker.input autocomplete="bday" />
                    </x-slot>
                </flux:date-picker>

                <!-- Phone -->
                <flux:input
                    wire:model.blur="phone"
                    :loading="true"
                    :label="__('fields.phone.label')"
                    type="tel"
                    required
                    autocomplete="tel"
                    :placeholder="__('fields.phone.placeholder')"
                >
                    <x-slot name="iconTrailing">
                        <flux:input-tooltip>
                            <p>{{ __('fields.phone.tooltip') }}</p>
                        </flux:input-tooltip>
                    </x-slot>
                </flux:input>
            @endif

            <!-- Password -->
            <flux:input
                wire:model="password"
                :label="__('fields.new-password.label')"
                type="password"
                required
                autofocus
                autocomplete="new-password"
                :placeholder="__('fields.new-password.placeholder')"
                :passwordrules="Rules\Password::defaults()->toPasswordRulesString()"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                wire:model="password_confirmation"
                :label="__('fields.password-confirmation.label')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('fields.password-confirmation.placeholder')"
                :passwordrules="Rules\Password::defaults()->toPasswordRulesString()"
                viewable
            />

            <flux:button
                type="submit"
                variant="primary"
                class="w-full"
            >{{ __('auth.recovery.complete.action') }}</flux:button>
        </form>
    @endif
</div>
