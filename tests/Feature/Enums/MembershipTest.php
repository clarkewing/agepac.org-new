<?php

use App\Enums\Products\Membership;
use App\Exceptions\MembershipNotFoundException;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;

beforeEach(function () {
    config()->set('cashier.products.membership', [
        'agepac' => 'prod_agepac_123',
        'agepac+alumni' => 'prod_alumni_456',
    ]);
});

it('maps fromStripeId to the correct enum case', function () {
    expect(Membership::fromStripeId('prod_agepac_123'))->toBe(Membership::AGEPAC)
        ->and(Membership::fromStripeId('prod_alumni_456'))->toBe(Membership::AGEPAC_ALUMNI);
});

it('throws when fromStripeId does not match any product', function () {
    Membership::fromStripeId('prod_unknown');
})->throws(MembershipNotFoundException::class);

it('returns the configured Stripe product id', function () {
    expect(Membership::AGEPAC->stripeProductId())->toBe('prod_agepac_123')
        ->and(Membership::AGEPAC_ALUMNI->stripeProductId())->toBe('prod_alumni_456');
});

it('provides translated label and description', function () {
    app()->setLocale('en');

    expect(Membership::AGEPAC)
        ->label()->toBe('AGEPAC Membership')
        ->description()->toContain('Access to a lively forum')
        ->and(Membership::AGEPAC_ALUMNI)
        ->label()->toBe('AGEPAC + ENAC Alumni Membership')
        ->description()->toContain('Includes all AGEPAC benefits');
});

it('returns the Stripe price associated with the product', function () {
    mockStripeClientWithResponse(stripeProductResponse('price_123'));

    expect(Membership::AGEPAC->stripePrice()->id)->toBe('price_123');
});

it('uses the flexible cache driver to return the Stripe price', function () {
    mockStripeClientWithResponse(stripeProductResponse('price_123'));

    // First call: should hit the API
    expect(Membership::AGEPAC->stripePrice()->id)->toBe('price_123');

    // Change Stripe response after first API request
    mockStripeClientWithResponse(stripeProductResponse('price_456'));

    // After 5 minutes: Should still hit cache
    $this->travel(5)->minutes();
    expect(Membership::AGEPAC->stripePrice()->id)->toBe('price_123');

    // After 10 minutes: Should have re-fetched from Stripe
    $this->travel(5)->minutes();
    expect(Membership::AGEPAC->stripePrice()->id)->toBe('price_456');
});

function mockStripeClientWithResponse(array $response, int $times = 1): void
{
    $mock = Mockery::mock(ClientInterface::class);

    $mock->shouldReceive('request')
        ->times($times)
        ->andReturn([json_encode($response), 200, []]);

    ApiRequestor::setHttpClient($mock);
}

function stripeProductResponse(string $priceId): array
{
    return [
        'id' => 'prod_agepac_123',
        'object' => 'product',
        'default_price' => [
            'id' => $priceId,
            'object' => 'price',
        ],
    ];
}
