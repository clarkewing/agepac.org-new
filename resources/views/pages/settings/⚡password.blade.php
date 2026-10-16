<?php

use App\Actions\Fortify\PasswordValidationRules;
use App\Livewire\Traits\InteractsWithToasts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Head\Facades\Head;
use Livewire\Component;

new class extends Component
{
    use InteractsWithToasts;
    use PasswordValidationRules;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->toast(__('settings.password.status.updated'), variant: 'success');
    }

    public function rendering(): void
    {
        Head::title(__('navigation.settings.password').' - '.__('settings.title'));
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('settings.password.heading')" :subheading="__('settings.password.subheading')">
        <form wire:submit="updatePassword" class="mt-6 space-y-6">
            <!-- Current Password -->
            <flux:input
                wire:model="current_password"
                :label="__('fields.current-password.label')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('fields.current-password.placeholder')"
                viewable
            />

            <!-- New Password -->
            <flux:input
                wire:model="password"
                :label="__('fields.new-password.label')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('fields.new-password.placeholder')"
                :passwordrules="Password::defaults()->toPasswordRulesString()"
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
                :passwordrules="Password::defaults()->toPasswordRulesString()"
                viewable
            />

            <flux:button variant="primary" type="submit"> {{ __('settings.password.action') }} </flux:button>
        </form>
    </x-settings.layout>
</section>
