<?php

namespace App\Enums\Products;

use App\Exceptions\MembershipNotFoundException;
use Illuminate\Support\Facades\Cache;
use Laravel\Cashier\Cashier;
use Stripe\Price;
use Stripe\Product;

enum Membership: string
{
    case AGEPAC = 'agepac';

    case AGEPAC_ALUMNI = 'agepac+alumni';

    /**
     * @throws \App\Exceptions\MembershipNotFoundException
     */
    public static function fromStripeProduct(string|Product $product): Membership
    {
        if ($product instanceof Product) {
            $product = $product->id;
        }

        if ($key = array_search($product, config('cashier.products.membership'), true)) {
            return Membership::from($key);
        }

        throw new MembershipNotFoundException("Could not find a membership product for the Stripe id [$product].");
    }

    /**
     * @throws \App\Exceptions\MembershipNotFoundException
     */
    public static function fromStripePrice(string|Price $price): Membership
    {
        return self::fromStripeProduct(self::getProductFromPrice($price));
    }

    public function label(): string
    {
        return __("products.membership.$this->value.name");
    }

    public function description(): string
    {
        return __("products.membership.$this->value.description");
    }

    public function stripeProductId(): string
    {
        return config("cashier.products.membership.$this->value");
    }

    public function stripePrice(): Price
    {
        return Cache::flexible(
            "membership.{$this->stripeProductId()}.amount",
            [300, 600],
            function () {
                $stripeProduct = Cashier::stripe()->products->retrieve(
                    $this->stripeProductId(),
                    ['expand' => ['default_price']],
                );

                return $stripeProduct->default_price;
            },
        );
    }

    protected static function getProductFromPrice(string|Price $price): Product|string
    {
        if (is_string($price)) {
            $price = Cashier::stripe()->prices->retrieve($price);
        }

        return $price->product;
    }
}
