<?php

use App\Actions\Fortify\ProfileValidationRules;
use App\Enums\ClassCourse;
use App\Enums\Gender;
use App\Livewire\Traits\InteractsWithToasts;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Head\Facades\Head;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use InteractsWithToasts;
    use ProfileValidationRules;

    public string $first_name = '';

    public string $last_name = '';

    public string $class_course = '';

    public string $class_year = '';

    public string $gender = '';

    public string $birth_date = '';

    public string $email = '';

    public string $phone = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->first_name = $this->user->first_name;
        $this->last_name = $this->user->last_name;
        $this->class_course = $this->user->class_course?->value ?? '';
        $this->class_year = (string) ($this->user->class_year ?? '');
        $this->gender = $this->user->gender ?? '';
        $this->birth_date = $this->user->birth_date?->format('Y-m-d') ?? '';
        $this->email = $this->user->email;
        $this->phone = $this->user->phone?->formatE164() ?? '';
    }

    public function updateProfileInformation(): void
    {
        $validated = $this->validate([
            ...$this->detailsRules(),
            'email' => $this->identityRules($this->user)['email'],
        ]);

        $this->user->fill($validated);

        if ($this->user->isDirty('email')) {
            $this->user->email_verified_at = null;
        }

        $this->user->save();

        if ($this->user->wasChanged('email')) {
            $this->user->sendEmailVerificationNotification();
        }

        $this->toast(__('settings.profile.status.saved'), variant: 'success');
    }

    public function resendVerificationNotification(): void
    {
        if ($this->user->hasVerifiedEmail()) {
            return;
        }

        $this->user->sendEmailVerificationNotification();

        $this->toast(__('settings.profile.email-verification.status.verification-link-sent'), variant: 'success');
    }

    #[Computed]
    public function user(): User
    {
        return Auth::user();
    }

    public function rendering(): void
    {
        Head::title(__('navigation.settings.profile').' - '.__('settings.title'));
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('settings.profile.heading')" :subheading="__('settings.profile.subheading')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="grid gap-6 sm:grid-cols-2">
                <!-- First Name -->
                <flux:input
                    wire:model="first_name"
                    :label="__('fields.first-name.label')"
                    type="text"
                    disabled
                    autocomplete="given-name"
                    :placeholder="__('fields.first-name.placeholder')"
                />

                <!-- Last Name -->
                <flux:input
                    wire:model="last_name"
                    :label="__('fields.last-name.label')"
                    type="text"
                    disabled
                    autocomplete="family-name"
                    :placeholder="__('fields.last-name.placeholder')"
                />
            </div>

            <div class="space-y-3">
                <div class="grid gap-6 sm:grid-cols-2">
                    <!-- Class Course -->
                    <flux:select
                        wire:model="class_course"
                        :label="__('fields.class-course.label')"
                        disabled
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
                        inputmode="numeric"
                        maxlength="4"
                        disabled
                        placeholder="YYYY"
                    />
                </div>

                <flux:description>
                    {{ __('settings.profile.locked.text') }}
                    <flux:link href="mailto:bureau@agepac.org">{{ __('settings.profile.locked.contact') }}</flux:link>
                </flux:description>
            </div>

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
            <flux:date-picker
                wire:model="birth_date"
                :label="__('fields.birth-date.label')"
                selectable-header
                :open-to="now()->subYears(20)->toDateString()"
            >
                <x-slot name="trigger">
                    <flux:date-picker.input required autocomplete="bday" :invalid="$errors->has('birth_date')" />
                </x-slot>
            </flux:date-picker>

            <!-- Email Address -->
            <div>
                <flux:input
                    wire:model="email"
                    :label="__('fields.email.label')"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="jp.celton@example.com"
                />

                @if (! $this->user->hasVerifiedEmail())
                    <div class="mt-4">
                        <flux:text>
                            <flux:icon name="exclamation-triangle" class="mr-px inline h-5 w-5 text-amber-400" />
                            {{ __('settings.profile.email-verification.prompt') }}

                            <flux:link as="button" class="text-sm" wire:click.prevent="resendVerificationNotification">
                                {{ __('settings.profile.email-verification.action') }}
                            </flux:link>
                        </flux:text>
                    </div>
                @endif
            </div>

            <!-- Phone -->
            <flux:phone
                wire:model="phone"
                :label="__('fields.phone.label')"
                required
                country="fr"
                :country-order="[
                    'fr', 're', 'gp', 'mq', 'gf', 'yt', 'nc', 'pf',
                    'be', 'ch', 'lu', 'mc',
                    'ca', 'de', 'es', 'it',
                ]"
            />

            <flux:button variant="primary" type="submit"> {{ __('settings.profile.action') }} </flux:button>
        </form>

        <livewire:pages::settings.delete-user-form />
    </x-settings.layout>
</section>
