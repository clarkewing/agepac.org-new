<flux:callout icon="light-bulb" color="sky">
    <flux:callout.heading>{{ __('settings.membership.callouts.sepa-enticement.heading') }}</flux:callout.heading>
    <flux:callout.text>
        {{ __('settings.membership.callouts.sepa-enticement.text') }}
        <flux:callout.link href="https://stripe.com/{{ config('app.locale') }}-fr/pricing#payments" target="_blank">
            {{ __('settings.membership.callouts.sepa-enticement.action') }}
        </flux:callout.link>
    </flux:callout.text>
</flux:callout>
