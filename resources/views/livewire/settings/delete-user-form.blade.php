<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('settings.profile.delete-account.heading') }}</flux:heading>
        <flux:subheading>{{ __('settings.profile.delete-account.subheading') }}</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
            {{ __('settings.profile.delete-account.action') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form wire:submit="deleteUser" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('settings.profile.delete-account.confirmation-modal.heading') }}
                </flux:heading>

                <flux:subheading>
                    {{ __('settings.profile.delete-account.confirmation-modal.subheading') }}
                </flux:subheading>
            </div>

            <flux:input wire:model="password" :label="__('fields.password.label')" type="password" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">
                        {{ __('settings.profile.delete-account.confirmation-modal.cancel-action') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">
                    {{ __('settings.profile.delete-account.action') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</section>
