<flux:callout icon="light-bulb" color="sky">
    <flux:callout.heading>{{ __('Choosing SEPA Direct Debit helps us keep membership costs low') }}</flux:callout.heading>
    <flux:callout.text>
        {{ __('It’s simpler for renewals and saves on fees — so more of your contribution goes where it matters.') }}
        <flux:callout.link href="https://stripe.com/{{ config('app.locale') }}-fr/pricing#payments" target="_blank">
            {{ __('Learn more') }}
        </flux:callout.link>
    </flux:callout.text>
</flux:callout>
