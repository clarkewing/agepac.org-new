<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.login.status.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.login.status.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
};
?>

<div class="flex flex-col gap-6">
    <flux:heading class="text-center" size="xl">{{ __('auth.login.heading') }}</flux:heading>

    @env('local')
        <div class="space-y-4">
            <x-login-link />
        </div>

        <flux:separator text="or" />
    @endenv

    <div class="flex flex-col gap-6">
        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit="login" class="flex flex-col gap-6">
            <!-- Email -->
            <flux:input
                wire:model="email"
                :label="__('fields.email.label')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:field>
                <div class="flex justify-between">
                    <flux:label>{{ __('fields.password.label') }}</flux:label>

                    @if (Route::has('password.request'))
                        <flux:link variant="subtle" class="text-sm" :href="route('password.request')" wire:navigate>
                            {{ __('auth.login.forgot-password') }}
                        </flux:link>
                    @endif
                </div>

                <flux:input
                    wire:model="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('fields.password.placeholder')"
                    viewable
                />
            </flux:field>

            <!-- Remember Me -->
            <flux:checkbox wire:model="remember" :label="__('auth.login.remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full">{{ __('auth.login.action') }}</flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <flux:subheading class="text-center text-zinc-600 dark:text-zinc-400">
                <span>{{ __('auth.login.register-prompt') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('auth.login.register-link') }}</flux:link>
            </flux:subheading>
        @endif
    </div>
</div>
