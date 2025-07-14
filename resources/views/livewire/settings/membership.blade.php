@use(App\Enums\Products\Membership)

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Membership')" :subheading=" __('Manage your AGEPAC membership')">
        <div class="space-y-4">
            @if($this->followsSuccessfulCheckout())
                <flux:callout icon="hand-thumb-up">
                    <flux:callout.heading>{{ __('Checkout completed') }}</flux:callout.heading>
                    <flux:callout.text>
                        {{ __('Your payment is being processed, it might be a few minutes before your new membership appears.') }}
                    </flux:callout.text>
                </flux:callout>
            @elseif($this->followsCanceledCheckout())
                <flux:callout icon="x-mark" variant="warning">
                    <flux:callout.heading>{{ __('Checkout interrupted') }}</flux:callout.heading>
                    <flux:callout.text>
                        {{ __('It looks like the checkout flow was interrupted. When you’re ready, please try again.') }}
                    </flux:callout.text>
                </flux:callout>
            @endif

            @if($this->subscription)
                @if($this->subscription->hasIncompletePayment())
                    <flux:callout icon="exclamation-circle" variant="danger">
                        @if($this->subscription->pastDue())
                            <flux:callout.heading>{{ __('Payment past due') }}</flux:callout.heading>
                            <flux:callout.text>
                                {{ __('The last payment for your membership failed.') }}
                            </flux:callout.text>
                        @elseif($this->subscription->incomplete())
                            <flux:callout.heading>{{ __('Incomplete payment') }}</flux:callout.heading>
                            <flux:callout.text>
                                {{ __('Your payment could not be completed and requires further action.') }}
                            </flux:callout.text>
                        @endif
                        <x-slot name="actions">
                            <flux:button
                                    size="sm"
                                    href="{{ route('cashier.payment', $this->subscription->latestPayment()->id) }}"
                            >
                                Complete payment
                            </flux:button>
                        </x-slot>
                    </flux:callout>
                @endif

                @if($this->subscription->onGracePeriod())
                    <flux:callout icon="exclamation-triangle" variant="warning">
                        <flux:callout.heading>{{ __('Auto-renew deactivated') }}</flux:callout.heading>
                        <flux:callout.text>
                            {{ __('You will no longer be an AGEPAC member on :date. You can resume your membership by clicking the button below.', ['date' => $this->subscription->ends_at->toFormattedDateString()]) }}
                        </flux:callout.text>
                        <x-slot name="actions">
                            <flux:button size="sm" wire:click="resume">
                                Resume membership
                            </flux:button>
                        </x-slot>
                    </flux:callout>
                @endif

                {{--@if($this->subscription->pending())--}}
                {{--    <flux:callout icon="information-circle" variant="info">--}}
                {{--        <flux:callout.heading>{{ __('Pending update') }}</flux:callout.heading>--}}
                {{--        <flux:callout.text>--}}
                {{--            {{ __('Your membership has a pending update that will be applied on your next billing cycle. To manage your membership, click the “Manage membership” button below.') }}--}}
                {{--        </flux:callout.text>--}}
                {{--    </flux:callout>--}}
                {{--@endif--}}

                @if($this->subscription->onTrial())
                    <flux:callout icon="information-circle" variant="warning">
                        <flux:callout.heading>{{ __('Trial membership') }}</flux:callout.heading>
                        <flux:callout.text>
                            {{ __('You’re currently on a trial membership that will end on :date. To ensure your membership continues beyond the trial period, click the “Manage membership” button below.', ['date' => $this->subscription->trial_ends_at->toFormattedDateString()]) }}
                        </flux:callout.text>
                    </flux:callout>
                @elseif($this->subscription->active())
                    <flux:callout icon="information-circle" variant="success">
                        <flux:callout.heading>{{ __('Membership active') }}</flux:callout.heading>
                        <flux:callout.text>
                            {{ __('You’re currently subscribed to the :plan plan. To manage or cancel your membership, click the “Manage membership” button below.', ['plan' => Membership::fromStripeId($this->subscription->items()->first()->stripe_product)->label()]) }}
                        </flux:callout.text>
                    </flux:callout>
                @elseif($this->subscription->ended())
                    <flux:callout icon="x-circle" variant="danger">
                        <flux:callout.heading>{{ __('Membership ended') }}</flux:callout.heading>
                        <flux:callout.text>
                            {{ __('Your membership has ended. To start a new membership, use the form below.') }}
                        </flux:callout.text>
                    </flux:callout>
                @endif
            @else
                <flux:callout icon="x-circle" variant="danger">
                    <flux:callout.heading>{{ __('Inactive membership') }}</flux:callout.heading>
                    <flux:callout.text>
                        {{ __('You do not have an active AGEPAC membership. To start a new membership, use the form below.') }}
                    </flux:callout.text>
                </flux:callout>
            @endif
        </div>

        <div class="mt-6">
            @if(! $this->subscription || $this->subscription?->ended())
                <livewire:settings.create-membership-form />
            @else
                <flux:button wire:click="openBillingPortal">{{ __('Manage membership') }}</flux:button>
            @endif
        </div>

        <div class="mt-4">
            @include('partials.membership-sepa-enticement-callout')
        </div>
    </x-settings.layout>
</section>
