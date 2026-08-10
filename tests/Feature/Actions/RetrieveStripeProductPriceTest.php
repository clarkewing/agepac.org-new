<?php

use App\Actions\RetrieveStripeProductPrice;
use Laravel\Cashier\Cashier;
use Stripe\Price;

beforeEach(function () {
    $this->action = resolve(RetrieveStripeProductPrice::class);

    $this->product = Cashier::stripe()->products->create([
        'name' => '[TESTING] Generic Product',
        'default_price_data' => [
            'currency' => 'eur',
            'unit_amount' => 3000,
        ],
    ]);
});

afterEach(function () {
    Cashier::stripe()->products->update($this->product->id, ['active' => false]);
});

it('retrieves the default price from Stripe product', function () {
    expect(($this->action)($this->product->id))
        ->toBeInstanceOf(Price::class)
        ->id->toBe($this->product->default_price);
})->group('stripe-api');
