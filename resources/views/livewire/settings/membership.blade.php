@use(App\Enums\Products\Membership)

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('settings.membership.heading')" :subheading="__('settings.membership.subheading')">
        <div class="space-y-4">
            @if($this->followsSuccessfulCheckout())
                <flux:callout icon="hand-thumb-up">
                    <flux:callout.heading>{{ __('settings.membership.callouts.checkout-completed.heading') }}</flux:callout.heading>
                    <flux:callout.text>{{ __('settings.membership.callouts.checkout-completed.text') }}</flux:callout.text>
                </flux:callout>
            @elseif($this->followsCanceledCheckout())
                <flux:callout icon="x-mark" variant="warning">
                    <flux:callout.heading>{{ __('settings.membership.callouts.checkout-interrupted.heading') }}</flux:callout.heading>
                    <flux:callout.text>{{ __('settings.membership.callouts.checkout-interrupted.text') }}</flux:callout.text>
                </flux:callout>
            @endif

            @if($this->subscription)
                @if($this->subscription->hasIncompletePayment())
                    @if($this->subscription->pastDue())
                        <flux:callout icon="exclamation-circle" variant="danger">
                            <flux:callout.heading>{{ __('settings.membership.callouts.subscription-past-due.heading') }}</flux:callout.heading>
                            <flux:callout.text>{{ __('settings.membership.callouts.subscription-past-due.text') }}</flux:callout.text>
                            <x-slot name="actions">
                                <flux:button
                                    size="sm"
                                    href="{{ route('cashier.payment', $this->subscription->latestPayment()->id) }}"
                                >
                                    {{ __('settings.membership.callouts.subscription-past-due.action') }}
                                </flux:button>
                            </x-slot>
                        </flux:callout>
                    @elseif($this->subscription->incomplete())
                        <flux:callout icon="exclamation-circle" variant="danger">
                            <flux:callout.heading>{{ __('settings.membership.callouts.subscription-incomplete.heading') }}</flux:callout.heading>
                            <flux:callout.text>{{ __('settings.membership.callouts.subscription-incomplete.text') }}</flux:callout.text>
                            <x-slot name="actions">
                                <flux:button
                                    size="sm"
                                    href="{{ route('cashier.payment', $this->subscription->latestPayment()->id) }}"
                                >
                                    {{ __('settings.membership.callouts.subscription-incomplete.action') }}
                                </flux:button>
                            </x-slot>
                        </flux:callout>
                    @endif
                @endif

                @if($this->subscription->onGracePeriod())
                    <flux:callout icon="exclamation-triangle" variant="warning">
                        <flux:callout.heading>{{ __('settings.membership.callouts.no-auto-renew.heading') }}</flux:callout.heading>
                        <flux:callout.text>
                            {{ __('settings.membership.callouts.no-auto-renew.text', ['date' => $this->subscription->ends_at->toFormattedDateString()]) }}
                        </flux:callout.text>
                        <x-slot name="actions">
                            <flux:button size="sm" wire:click="resume">
                                {{ __('settings.membership.callouts.no-auto-renew.action') }}
                            </flux:button>
                        </x-slot>
                    </flux:callout>
                @endif

                {{--@if($this->subscription->pending())--}}
                {{--    <flux:callout icon="information-circle" variant="info">--}}
                {{--        <flux:callout.heading>{{ __('settings.membership.callouts.update-pending.heading') }}</flux:callout.heading>--}}
                {{--        <flux:callout.text>{{ __('settings.membership.callouts.update-pending.text') }}</flux:callout.text>--}}
                {{--    </flux:callout>--}}
                {{--@endif--}}

                @if($this->subscription->onTrial())
                    <flux:callout icon="information-circle" variant="warning">
                        <flux:callout.heading>{{ __('settings.membership.callouts.subscription-trial.heading') }}</flux:callout.heading>
                        <flux:callout.text>
                            {{ __('settings.membership.callouts.subscription-trial.text', ['date' => $this->subscription->trial_ends_at->toFormattedDateString()]) }}
                        </flux:callout.text>
                    </flux:callout>
                @elseif($this->subscription->active())
                    <flux:callout icon="information-circle" variant="success">
                        <flux:callout.heading>{{ __('settings.membership.callouts.subscription-active.heading') }}</flux:callout.heading>
                        <flux:callout.text>
                            {{ __('settings.membership.callouts.subscription-active.text', [
                                'plan' => ($this->subscription->items->isNotEmpty()
                                    ? Membership::fromStripeProduct($this->subscription->items->first()->stripe_product)
                                    : Membership::fromStripePrice($this->subscription->stripe_price)
                                )->label(),
                            ]) }}
                        </flux:callout.text>
                    </flux:callout>
                @elseif($this->subscription->ended())
                    <flux:callout icon="x-circle" variant="danger">
                        <flux:callout.heading>{{ __('settings.membership.callouts.subscription-ended.heading') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('settings.membership.callouts.subscription-ended.text') }}</flux:callout.text>
                    </flux:callout>
                @endif
            @else
                <flux:callout icon="x-circle" variant="danger">
                    <flux:callout.heading>{{ __('settings.membership.callouts.subscription-inactive.heading') }}</flux:callout.heading>
                    <flux:callout.text>{{ __('settings.membership.callouts.subscription-inactive.text') }}</flux:callout.text>
                </flux:callout>
            @endif
        </div>

        <div class="mt-6">
            @if(! $this->subscription || $this->subscription?->ended())
                <livewire:settings.create-membership-form />
            @else
                <flux:button wire:click="openBillingPortal">{{ __('settings.membership.manage-action') }}</flux:button>
            @endif
        </div>

        <div class="mt-4">
            @include('partials.membership-sepa-enticement-callout')
        </div>
    </x-settings.layout>
</section>
