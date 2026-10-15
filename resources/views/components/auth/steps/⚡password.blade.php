<?php

use App\Actions\Fortify\PasswordValidationRules;
use App\Livewire\Traits\SfcStep;
use Illuminate\Validation\Rules\Password;
use Spatie\LivewireWizard\Components\StepComponent;

/**
 * Password step, shared by registration and account recovery. A wizard
 * that ends on this step names the action its button performs.
 */
new class extends StepComponent
{
    use PasswordValidationRules;

    // TODO: Drop once spatie/laravel-livewire-wizard#159 ships.
    use SfcStep;

    public string $password = '';

    public string $password_confirmation = '';

    public function save(): void
    {
        $this->validate([
            'password' => $this->passwordRules(),
        ]);

        $this->nextStep();
    }
};
?>

<form wire:submit="save" class="flex flex-col gap-6">
    <!-- Password -->
    <flux:input
        wire:model="password"
        :label="__('fields.password.label')"
        type="password"
        required
        autofocus
        autocomplete="new-password"
        :placeholder="__('fields.password.placeholder')"
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

    @include('partials.step-actions', ['lastStepLabel' => __('auth.recovery.action')])
</form>
