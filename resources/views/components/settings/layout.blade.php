<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            {{-- TODO: Add wire:navigate --}}
            <flux:navlist.item :href="route('settings.profile')">{{ __('Profile') }}</flux:navlist.item>
            {{-- TODO: Add wire:navigate --}}
            <flux:navlist.item :href="route('settings.password')">{{ __('Password') }}</flux:navlist.item>
            <flux:navlist.item :href="route('settings.membership')" wire:navigate>{{ __('Membership') }}</flux:navlist.item>
            <flux:navlist.item :href="route('settings.appearance')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
