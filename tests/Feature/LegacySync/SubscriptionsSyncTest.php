<?php

use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;
use Stripe\Subscription as StripeSubscription;
use Tests\Helpers\LegacySyncHelpers as Helpers;

beforeEach(function () {
    Helpers::setupLegacyDatabase();
});

test('subscription is synced to legacy database when saved', function () {
    $subscription = Subscription::factory()->create([
        'type' => 'membership',
        'stripe_id' => 'sub_test_123456',
        'stripe_price' => 'price_test_123456',
    ]);

    Helpers::verifyLegacySync('subscriptions', $subscription->id, [
        'user_id' => $subscription->user->id,
        'name' => 'membership', // Mapped from type
        'stripe_id' => 'sub_test_123456',
        'stripe_plan' => 'price_test_123456', // Mapped from stripe_price
    ]);
});

test('subscription updates are synced to legacy database', function () {
    $subscription = Subscription::factory()->create([
        'type' => 'membership',
        'stripe_id' => 'sub_test_123456',
        'stripe_price' => 'price_test_123456',
    ]);

    $subscription->type = 'donation';
    $subscription->stripe_status = StripeSubscription::STATUS_PAST_DUE;
    $subscription->save();

    Helpers::verifyLegacySync('subscriptions', $subscription->id, [
        'name' => 'donation', // Mapped from type
        'stripe_status' => StripeSubscription::STATUS_PAST_DUE,
    ]);
});

test('subscription items are synced to legacy database', function () {
    $subscription = Subscription::factory()
        ->state([
            'stripe_id' => 'sub_test_123456',
            'stripe_price' => 'price_test_123456',
        ])
        ->has(SubscriptionItem::factory()->state([
            'stripe_id' => 'si_test_123456',
            'stripe_product' => 'prod_test_123456',
            'stripe_price' => 'price_test_123456',
        ]), 'items')
        ->create();
    $subscriptionItem = $subscription->items->first();

    Helpers::verifyLegacySync('subscription_items', $subscriptionItem->id, [
        'subscription_id' => $subscription->id,
        'stripe_id' => 'si_test_123456',
        'stripe_plan' => 'price_test_123456', // Mapped from stripe_price
    ]);

    // Check that the default value for stripe_product is not present in legacy
    // as it's excluded in the config
    $legacyItem = DB::connection('legacy')
        ->table('subscription_items')
        ->where('id', $subscriptionItem->id)
        ->first();

    expect($legacyItem)->not->toHaveKey('stripe_product');
});
