<?php

namespace App\Enums\Products;

use App\Exceptions\MembershipNotFoundException;
use Illuminate\Support\Facades\Cache;
use Laravel\Cashier\Cashier;
use Stripe\Price;

enum Membership: string
{
    case AGEPAC = 'agepac';

    case AGEPAC_ALUMNI = 'agepac+alumni';

    /**
     * @throws \App\Exceptions\MembershipNotFoundException
     */
    public static function fromStripeId(string $id): Membership
    {
        if ($key = array_search($id, config('cashier.products.membership'), true)) {
            return Membership::from($key);
        }

        throw new MembershipNotFoundException("Could not find a membership product for the Stripe id [$id].");
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
}
