<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use Laravel\Cashier\Checkout;

class CreateSubscriptionCheckout
{
    public function __invoke(
        User $user,
        array|string $prices,
        string $successUrl,
        string $cancelUrl,
        string $type = 'default',
        array $metadata = [],
        array $sessionOptions = [],
        array $customerOptions = []
    ): Checkout {
        return $user
            ->newSubscription($type, $prices)
            ->allowPromotionCodes()
            ->checkout(array_merge(array_filter([
                'success_url' => $this->withStripeSession(Uri::of($successUrl)),
                'cancel_url' => $this->withStripeSession(Uri::of($cancelUrl)->withQuery(['checkout_canceled' => true])),
                'metadata' => $metadata,
            ]), $sessionOptions), $customerOptions);
    }

    protected function withStripeSession(Uri $uri): string
    {
        $uri = $uri->withQuery(['session_id' => 'CHECKOUT_SESSION_ID']);

        // Ensure CHECKOUT_SESSION_ID is wrapped in curly braces for Stripe
        // Required because Uri encodes these characters
        return Str::replace('CHECKOUT_SESSION_ID', '{CHECKOUT_SESSION_ID}', $uri);
    }
}
