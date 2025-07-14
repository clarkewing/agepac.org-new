<?php

namespace App\Listeners;

use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookHandled;
use Laravel\Cashier\Events\WebhookReceived;
use Stripe\Stripe;

class StripeEventListener
{
    public function handle(WebhookReceived $event): void
    {
        $method = 'handle'.Str::studly(str_replace('.', '_', $event->payload['type']));

        if (method_exists($this, $method)) {
            $this->setMaxNetworkRetries();

            $this->{$method}($event->payload);

            WebhookHandled::dispatch($event->payload);
        }
    }

    protected function handlePaymentMethodAttached(array $payload): void
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if (! $user) {
            return;
        }

        if ($user->hasDefaultPaymentMethod()) {
            return;
        }

        $user->updateDefaultPaymentMethod($payload['data']['object']['id']);
    }

    /** @return \Laravel\Cashier\Billable|null */
    protected function getUserByStripeId($stripeId)
    {
        return Cashier::findBillable($stripeId);
    }

    protected function setMaxNetworkRetries($retries = 3): void
    {
        Stripe::setMaxNetworkRetries($retries);
    }
}
