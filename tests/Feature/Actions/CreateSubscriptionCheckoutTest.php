<?php

use App\Actions\CreateSubscriptionCheckout;
use App\Enums\Products\Membership;
use App\Models\User;
use Illuminate\Support\Uri;

it('creates a checkout with correct options', function () {
    $action = new CreateSubscriptionCheckout;
    $user = User::factory()->asCustomer()->create();
    $agepacPrice = Membership::AGEPAC->stripePrice()->id;

    $result = $action(
        $user,
        $agepacPrice,
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
        ->allow_promotion_codes->toBeTrue()
        ->url->toStartWith('https://checkout.stripe.com/c/pay')
        ->customer->toBe($user->stripe_id)
        ->success_url->toBe('https://example.com/success?session_id={CHECKOUT_SESSION_ID}')
        ->cancel_url->toBe('https://example.com/cancel?checkout_canceled=1&session_id={CHECKOUT_SESSION_ID}')
        ->metadata->toBe(['foo' => 'bar']);
})->group('stripe', 'api');

it('appends the session_id placeholder to redirect URLs', function () {
    $result = invade(new CreateSubscriptionCheckout)->withStripeSession(Uri::of('https://example.com/path'));

    expect($result)->toBe('https://example.com/path?session_id={CHECKOUT_SESSION_ID}');
});
