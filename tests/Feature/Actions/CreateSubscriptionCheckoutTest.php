<?php

use App\Actions\CreateSubscriptionCheckout;
use App\Models\User;
use Illuminate\Support\Uri;
use Laravel\Cashier\Cashier;

beforeEach(function () {
    $this->action = resolve(CreateSubscriptionCheckout::class);
});

describe('checkout creation', function () {
    beforeEach(function () {
        $this->user = User::factory()->asCustomer()->create();
        $this->price = Cashier::stripe()->prices->create([
            'product_data' => ['name' => '[TESTING] Membership'],
            'currency' => 'eur',
            'unit_amount' => 3000,
            'recurring' => ['interval' => 'year'],
        ]);
    });

    afterEach(function () {
        Cashier::stripe()->customers->delete($this->user->stripe_id);
        Cashier::stripe()->products->update($this->price->product, ['active' => false]);
    });

    it('creates a checkout with correct options', function () {
        $result = ($this->action)(
            $this->user,
            $this->price->id,
            'https://example.com/success',
            'https://example.com/cancel',
            'default',
            ['foo' => 'bar'],
            ['payment_method_data' => ['allow_redisplay' => 'always']],
            ['baz' => 'qux'],
        );

        expect($result->asStripeCheckoutSession()->toArray())
            ->mode->toBe('subscription')
            ->status->toBe('open')
            ->locale->toBe(app()->getLocale())
            ->allow_promotion_codes->toBeTrue()
            ->url->toStartWith('https://checkout.stripe.com/c/pay')
            ->customer->toBe($this->user->stripe_id)
            ->success_url->toBe('https://example.com/success?session_id={CHECKOUT_SESSION_ID}')
            ->cancel_url->toBe('https://example.com/cancel?checkout_canceled=1&session_id={CHECKOUT_SESSION_ID}')
            ->metadata->toBe(['foo' => 'bar']);
    });
})->group('stripe-api');

it('appends the session_id placeholder to redirect URLs', function () {
    expect(invade($this->action)->withStripeSession(Uri::of('https://example.com/path')))
        ->toBe('https://example.com/path?session_id={CHECKOUT_SESSION_ID}');
});
