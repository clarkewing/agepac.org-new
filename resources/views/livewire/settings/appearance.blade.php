<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('settings.appearance.heading')" :subheading=" __('settings.appearance.subheading')">
        <div class="my-6 w-full space-y-6">
            <flux:radio.group
                variant="segmented"
                x-data
                x-model="$flux.appearance"
                :label="__('fields.theme.label')"
            >
                <flux:radio value="light" icon="sun">
                    {{ __('fields.theme.options.light') }}
                </flux:radio>
                <flux:radio value="dark" icon="moon">
                    {{ __('fields.theme.options.dark') }}
                </flux:radio>
                <flux:radio value="system" icon="computer-desktop">
                    {{ __('fields.theme.options.system') }}
                </flux:radio>
            </flux:radio.group>
        </div>
    </x-settings.layout>
</section>
