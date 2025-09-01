<?php

use App\Actions\RetrieveStripeProductPrice;
use Stripe\Price;
use Tests\Helpers\StripeHelpers;

afterEach(function () {
    StripeHelpers::cleanup();
});

it('retrieves the default price from Stripe product', function () {
    StripeHelpers::mockStripeClientWithResponse(StripeHelpers::stripeProductResponse('price_123'));

    $action = resolve(RetrieveStripeProductPrice::class);

    expect($action('prod_test_123'))
        ->toBeInstanceOf(Price::class)
        ->id->toBe('price_123');
});
