<?php

namespace App\Livewire\Settings;

use Illuminate\Routing\Redirector;
use Laravel\Cashier\Subscription;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Membership extends Component
{
    public function mount(): void
    {
        $this->handleCheckoutReturn();
    }

    #[Computed]
    public function subscription(): ?Subscription
    {
        return auth()->user()->subscription('membership');
    }

    public function openBillingPortal(): Redirector
    {
        // if (! auth()->user()->hasStripeId()) {
        //     auth()->user()->createAsStripeCustomer();
        // }

        return redirect(
            auth()->user()->billingPortalUrl(route('settings.membership'))
        );
    }

    public function resume(): void
    {
        $this->subscription()->resume();
    }

    protected function handleCheckoutReturn(): void
    {
        $this->js("history.pushState({}, '', location.pathname)");
    }

    public function followsSuccessfulCheckout(): bool
    {
        return ! request()->boolean('checkout_canceled') && request()->has('session_id');
    }

    public function followsCanceledCheckout(): bool
    {
        return request()->boolean('checkout_canceled');
    }
}
