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
