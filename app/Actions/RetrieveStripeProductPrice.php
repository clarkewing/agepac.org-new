<?php

namespace App\Actions;

use Laravel\Cashier\Cashier;
use Stripe\Price;

class RetrieveStripeProductPrice
{
    /**
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function __invoke(string $productId): Price
    {
        $stripeProduct = Cashier::stripe()->products->retrieve(
            $productId,
            ['expand' => ['default_price']],
        );

        return $stripeProduct->default_price;
    }
}
