<?php

use App\Actions\SendVerificationCode;
use App\Livewire\Traits\InteractsWithToasts;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Verifies the user using a one-time-code emailed to them.
 */
new class extends Component
{
    use InteractsWithToasts;

    public User $user;

    #[Validate(['required', 'digits:6'])]
    public string $verification_code = '';

    public function mount(SendVerificationCode $send): void
    {
        $this->resetVerification();

        if (! $send->isPending($this->user)) {
            $send($this->user);
        }
    }

    public function verify(): void
    {
        $this->validate();

        $result = $this->user->consumeOneTimePassword($this->verification_code);

        if (! $result->isOk()) {
            throw ValidationException::withMessages([
                'verification_code' => $result->validationMessage(),
            ]);
        }

        $this->markVerified();

        $this->dispatch('code-verified');
    }

    public function resendCode(SendVerificationCode $send): void
    {
        $send($this->user);

        unset($this->resendAvailableAt);

        $this->toast(__('auth.verification-code.resent'), variant: 'success');
    }

    #[Computed]
    public function resendAvailableAt(): int
    {
        return app(SendVerificationCode::class)->availableAt($this->user) ?? now()->getTimestamp();
    }

    protected function resetVerification(): void
    {
        Session::forget('email-verified');
    }

    protected function markVerified(): void
    {
        Session::put('email-verified', ['user_id' => $this->user->getKey(), 'at' => now()->getTimestamp()]);
    }
};
?>

<div class="flex flex-col gap-6">
    <form wire:submit="verify" class="flex flex-col items-center gap-6">
        <flux:otp
            wire:model="verification_code"
            :label="__('auth.verification-code.code-label')"
            length="6"
            submit="auto"
            x-init="$nextTick(() => $el.querySelector('input')?.focus())"
        />

        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('auth.verification-code.action') }}
        </flux:button>
    </form>

    <flux:subheading class="text-center text-zinc-600 dark:text-zinc-400">
        <x-countdown wire:key="resend-{{ $this->resendAvailableAt }}" :until="$this->resendAvailableAt">
            {{ __('auth.verification-code.resend-wait') }}

            <x-slot:done>
                {{ __('auth.verification-code.resend-prompt') }}
                <flux:link as="button" wire:click="resendCode">
                    {{ __('auth.verification-code.resend-link') }}
                </flux:link>
            </x-slot:done>
        </x-countdown>
    </flux:subheading>
</div>
