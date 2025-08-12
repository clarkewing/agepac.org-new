@use(Illuminate\Contracts\Auth\MustVerifyEmail)

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('settings.profile.heading')" :subheading="__('settings.profile.subheading')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input
                wire:model="name"
                :label="__('fields.name.label')"
                type="text"
                required
                autofocus
                autocomplete="name"
            />

            <div>
                <flux:input
                    wire:model="email"
                    :label="__('fields.email.label')"
                    type="email"
                    required
                    autocomplete="email"
                />

                @if (auth()->user() instanceof MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div class="mt-4">
                        <flux:text>
                            <flux:icon name="exclamation-triangle" class="inline mr-px h-5 w-5 text-amber-400" />
                            {{ __('settings.profile.email-verification.prompt') }}

                            <flux:link
                                class="text-sm cursor-pointer"
                                wire:click.prevent="resendVerificationNotification"
                            >
                                {{ __('settings.profile.email-verification.action') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('settings.profile.email-verification.status.verification-link-sent') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">
                        {{ __('settings.profile.action') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated" />
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
