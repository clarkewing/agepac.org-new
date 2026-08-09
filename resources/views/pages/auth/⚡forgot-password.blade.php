<?php

use Illuminate\Support\Facades\Password;
use Laravel\Head\Facades\Head;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('auth.forgot-password.status.link-sent'));
    }

    public function rendering(): void
    {
        Head::title(__('auth.forgot-password.title'));
    }
};
?>

 <div class="flex flex-col gap-6">
    <x-auth-header :title="__('auth.forgot-password.heading')" :description="__('auth.forgot-password.description')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('fields.email.label')"
            type="email"
            required
            autofocus
            placeholder="email@example.com"
        />

        <flux:button variant="primary" type="submit" class="w-full">{{ __('auth.forgot-password.action') }}</flux:button>
    </form>

    <flux:subheading class="text-center text-zinc-600 dark:text-zinc-400">
        <span>{{ __('auth.forgot-password.login-prompt') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('auth.forgot-password.login-link') }}</flux:link>
    </flux:subheading>
</div>
