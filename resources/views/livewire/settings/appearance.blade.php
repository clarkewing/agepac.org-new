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

            <flux:select
                variant="listbox"
                wire:model="language"
                wire:change="setLocale"
                :label="__('fields.language.label')"
                :placeholder="__('fields.language.placeholder')"
            >
                <flux:select.option value="en">
                    <div class="flex items-center gap-2">
                        <x-flag-language-en-us class="size-5" />
                        {{ __('fields.language.options.en') }}
                    </div>
                </flux:select.option>
                <flux:select.option value="fr">
                    <div class="flex items-center gap-2">
                        <x-flag-language-fr class="size-5" />
                        {{ __('fields.language.options.fr') }}
                    </div>
                </flux:select.option>
            </flux:select>
        </div>
    </x-settings.layout>
</section>
