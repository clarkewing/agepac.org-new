<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('settings.password.heading')" :subheading="__('settings.password.subheading')">
        <form wire:submit="updatePassword" class="mt-6 space-y-6">
            <flux:input
                wire:model="current_password"
                :label="__('fields.current-password.label')"
                type="password"
                required
                autocomplete="current-password"
            />
            <flux:input
                wire:model="password"
                :label="__('fields.new-password.label')"
                type="password"
                required
                autocomplete="new-password"
            />
            <flux:input
                wire:model="password_confirmation"
                :label="__('fields.password-confirmation.label')"
                type="password"
                required
                autocomplete="new-password"
            />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('settings.password.action') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated" />
            </div>
        </form>
    </x-settings.layout>
</section>
