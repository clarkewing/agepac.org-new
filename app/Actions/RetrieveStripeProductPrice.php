<?php

namespace App\Actions;

use Laravel\Cashier\Cashier;
use Stripe\Exception\ApiErrorException;
use Stripe\Price;

class RetrieveStripeProductPrice
{
    /**
     * @throws ApiErrorException
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
