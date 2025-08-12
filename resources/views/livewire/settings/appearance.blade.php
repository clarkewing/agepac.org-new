<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('settings.appearance.heading')" :subheading=" __('settings.appearance.subheading')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('settings.appearance.theme.options.light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('settings.appearance.theme.options.dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('settings.appearance.theme.options.system') }}</flux:radio>
        </flux:radio.group>
    </x-settings.layout>
</section>
