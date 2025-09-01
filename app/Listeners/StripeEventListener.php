<?php

namespace App\Listeners;

use App\Actions\RetrieveStripeProductPrice;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
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

    protected function handlePriceUpdated(array $payload): void
    {
        $this->cacheProductPrice($payload['data']['object']['product']);
    }

    protected function handleProductUpdated(array $payload): void
    {
        $this->cacheProductPrice($payload['data']['object']['id']);
    }

    /**
     * @return \Laravel\Cashier\Billable|\App\Models\User|null
     */
    protected function getUserByStripeId(string $stripeId): ?User
    {
        return Cashier::findBillable($stripeId);
    }

    protected function cacheProductPrice(mixed $productId): void
    {
        Cache::forever(
            "membership.$productId.price",
            resolve(RetrieveStripeProductPrice::class)($productId),
        );
    }

    protected function setMaxNetworkRetries(int $retries = 3): void
    {
        Stripe::setMaxNetworkRetries($retries);
    }
}
