<div class="flex flex-col gap-6">
    <x-auth-header :title="__('auth.confirm-password.heading')" :description="__('auth.confirm-password.description')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="confirmPassword" class="flex flex-col gap-6">
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

        <flux:button variant="primary" type="submit" class="w-full">{{ __('auth.confirm-password.action') }}</flux:button>
    </form>
</div>
