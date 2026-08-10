<?php

use App\Enums\Products\Membership;
use App\Exceptions\MembershipNotFoundException;
use Illuminate\Support\Facades\Cache;
use Stripe\Price;
use Stripe\Product;
use Tests\Concerns\InteractsWithStripe;

uses(InteractsWithStripe::class);

beforeEach(function () {
    config()->set('cashier.products.membership', [
        'agepac' => 'prod_agepac_123',
        'agepac+alumni' => 'prod_alumni_456',
    ]);
});

it('maps fromStripeProduct to the correct enum case', function () {
    expect(Membership::fromStripeProduct('prod_agepac_123'))->toBe(Membership::AGEPAC);
    expect(Membership::fromStripeProduct('prod_alumni_456'))->toBe(Membership::AGEPAC_ALUMNI);
});

it('maps fromStripeProduct with Product object to the correct enum case', function () {
    $productAgepac = new Product('prod_agepac_123');
    $productAlumni = new Product('prod_alumni_456');

    expect(Membership::fromStripeProduct($productAgepac))->toBe(Membership::AGEPAC);
    expect(Membership::fromStripeProduct($productAlumni))->toBe(Membership::AGEPAC_ALUMNI);
});

it('throws an exception when fromStripeProduct does not match any product', function () {
    expect(function () {
        Membership::fromStripeProduct('prod_unknown');
    })->toThrow(MembershipNotFoundException::class);

    expect(function () {
        $product = new Product('prod_unknown');

        Membership::fromStripeProduct($product);
    })->toThrow(MembershipNotFoundException::class);
});

it('maps fromStripePrice with string to the correct enum case', function () {
    $this->mockStripe([
        '/v1/prices/price_123' => $this->priceResponse('price_123', 'prod_agepac_123'),
        '/v1/prices/price_456' => $this->priceResponse('price_456', 'prod_alumni_456'),
    ]);

    expect(Membership::fromStripePrice('price_123'))->toBe(Membership::AGEPAC);
    expect(Membership::fromStripePrice('price_456'))->toBe(Membership::AGEPAC_ALUMNI);
});

it('maps fromStripePrice with Price object to the correct enum case', function () {
    $priceAgepac = tap(new Price('price_123'), fn ($price) => $price->product = 'prod_agepac_123');
    $priceAlumni = tap(new Price('price_456'), fn ($price) => $price->product = 'prod_alumni_456');

    expect(Membership::fromStripePrice($priceAgepac))->toBe(Membership::AGEPAC);
    expect(Membership::fromStripePrice($priceAlumni))->toBe(Membership::AGEPAC_ALUMNI);
});

it('throws when fromStripePrice does not match any product', function () {
    $this->mockStripe([
        '/v1/prices/price_unknown' => $this->priceResponse('price_unknown', 'prod_unknown'),
    ]);

    expect(function () {
        Membership::fromStripePrice('price_unknown');
    })->toThrow(MembershipNotFoundException::class);

    expect(function () {
        $price = tap(new Price('price_unknown'), fn ($price) => $price->product = 'prod_unknown');

        Membership::fromStripePrice($price);
    })->toThrow(MembershipNotFoundException::class);
});

it('returns the configured Stripe product id', function () {
    expect(Membership::AGEPAC->stripeProductId())->toBe('prod_agepac_123');
    expect(Membership::AGEPAC_ALUMNI->stripeProductId())->toBe('prod_alumni_456');
});

it('provides translated label and description', function () {
    app()->setLocale('en');

    expect(Membership::AGEPAC)
        ->label()->toBe('AGEPAC Membership')
        ->description()->toContain('Access to a lively forum');
    expect(Membership::AGEPAC_ALUMNI)
        ->label()->toBe('AGEPAC + ENAC Alumni Membership')
        ->description()->toContain('Includes all AGEPAC benefits');
});

it('returns the Stripe price associated with the product', function () {
    $this->mockStripe([
        '/v1/products/prod_agepac_123' => $this->productResponse('prod_agepac_123', 'price_123'),
    ]);

    expect(Membership::AGEPAC->stripePrice()->id)->toBe('price_123');
});

it('uses cache to return the Stripe price', function () {
    $this->mockStripe([
        '/v1/products/prod_agepac_123' => $this->productResponse('prod_agepac_123', 'price_123'),
    ]);

    // First call: should hit the API
    expect(Membership::AGEPAC->stripePrice()->id)->toBe('price_123');

    // Change Stripe response after first API request
    $this->mockStripe([
        '/v1/products/prod_agepac_123' => $this->productResponse('prod_agepac_123', 'price_456'),
    ]);

    // Should still hit cache and return the cached value
    expect(Membership::AGEPAC->stripePrice()->id)->toBe('price_123');

    // Clear the cache manually to simulate webhook clearing cache
    Cache::forget('membership.prod_agepac_123.price');

    // Should now fetch the new price from Stripe
    expect(Membership::AGEPAC->stripePrice()->id)->toBe('price_456');
});

it('caches product from price for 24 hours', function () {
    $this->mockStripe([
        '/v1/prices/price_123' => $this->priceResponse('price_123', Membership::AGEPAC->stripeProductId()),
    ]);

    // First call: should hit the API and cache the result
    expect(Membership::fromStripePrice('price_123'))->toBe(Membership::AGEPAC);

    // Change Stripe response after first API request
    $this->mockStripe([
        '/v1/prices/price_123' => $this->priceResponse('price_123', Membership::AGEPAC_ALUMNI->stripeProductId()),
    ]);

    // Should still hit cache and return the cached product (AGEPAC, not AGEPAC_ALUMNI)
    expect(Membership::fromStripePrice('price_123'))->toBe(Membership::AGEPAC);

    // Travel forward 25 hours to expire the 24-hour cache
    $this->travel(25)->hours();

    // Should now fetch the new product from Stripe
    expect(Membership::fromStripePrice('price_123'))->toBe(Membership::AGEPAC_ALUMNI);
});
