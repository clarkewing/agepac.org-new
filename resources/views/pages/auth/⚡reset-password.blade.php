<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component
{
    #[Locked]
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PasswordReset) {
            $this->addError('email', $this->getTranslationString($status));

            return;
        }

        Session::flash('status', $this->getTranslationString($status));

        $this->redirectRoute('login', navigate: true);
    }

    protected function getTranslationString(string $status): string
    {
        return __(match ($status) {
            Password::RESET_LINK_SENT => 'auth.reset-password.status.sent',
            Password::PASSWORD_RESET => 'auth.reset-password.status.reset',
            Password::INVALID_USER => 'auth.reset-password.status.user',
            Password::INVALID_TOKEN => 'auth.reset-password.status.token',
            Password::RESET_THROTTLED => 'auth.reset-password.status.throttled',
        });
    }
};
?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('auth.reset-password.heading')" :description="__('auth.reset-password.description')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="resetPassword" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('fields.email.label')"
            type="email"
            required
            autocomplete="email"
            :placeholder="__('fields.email.placeholder')"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('fields.password.label')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('fields.password.placeholder')"
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
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('auth.reset-password.action') }}
            </flux:button>
        </div>
    </form>
</div>
