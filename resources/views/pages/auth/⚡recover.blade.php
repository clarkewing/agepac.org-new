<?php

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use App\Services\Auth\AccountRecovery;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Head\Facades\Head;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component
{
    use PasswordValidationRules;

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        if (! $this->user?->requiresRecovery()) {
            AccountRecovery::cancel();

            $this->redirectRoute('login', navigate: true);

            return;
        }

        $this->continueIfMigrating();
    }

    #[On('code-verified')]
    public function codeVerified(): void
    {
        unset($this->verified);

        $this->continueIfMigrating();
    }

    public function resetPassword(): void
    {
        abort_unless($this->verified && $this->user->requiresPasswordReset(), 403);

        $validated = $this->validate([
            'password' => $this->passwordRules(),
        ]);

        AccountRecovery::complete($this->user, $validated);

        $this->redirect(
            app(LoginResponse::class)->toResponse(request())->getTargetUrl()
        );
    }

    #[Computed]
    public function user(): ?User
    {
        return AccountRecovery::user();
    }

    #[Computed]
    public function verified(): bool
    {
        return AccountRecovery::isVerified();
    }

    protected function continueIfMigrating(): void
    {
        if ($this->verified && $this->user->requiresMigration()) {
            $this->redirectRoute('recovery.migrate', navigate: true);
        }
    }

    public function rendering(): void
    {
        Head::title(__('auth.recovery.title'));
    }
};
?>

<div class="flex flex-col gap-6">
    @if (! $this->verified)
        <x-auth-header
            :title="__('auth.recovery.heading.'.($this->user->requiresMigration() ? 'migration' : 'password-reset'))"
            :description="__('auth.recovery.steps.verify.description')"
        />

        @if ($this->user->requiresPasswordReset())
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

        <livewire:auth.verification-code :user="$this->user" />
    @elseif ($this->user->requiresPasswordReset())
        <x-auth-header
            :title="__('auth.recovery.heading.password-reset')"
            :description="__('auth.recovery.steps.password.description')"
        />

        <form wire:submit="resetPassword" class="flex flex-col gap-6">
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

            <flux:button type="submit" variant="primary" class="w-full"> {{ __('auth.recovery.action') }} </flux:button>
        </form>
    @endif
</div>
