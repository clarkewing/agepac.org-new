<?php

namespace Tests\Feature\Observers;

use App\Models\User;
use App\Services\Mailcoach\Facades\Mailcoach;
use Laravel\Cashier\Subscription;
use Stripe\Subscription as StripeSubscription;

function createSubscriptionForUser(User $user, array $overrides = []): Subscription
{
    return Subscription::factory()->create(array_merge([
        'user_id' => $user->id,
        'type' => 'membership',
    ], $overrides));
}

it('adds members_newsletter tag when a subscription is saved and active', function () {
    $user = User::factory()->create();

    expect(Mailcoach::getSubscriber($user->email)->tags)
        ->toContain('newsletter')
        ->not->toContain('members_newsletter');

    createSubscriptionForUser($user);

    expect(Mailcoach::getSubscriber($user->email)->tags)
        ->toContain('members_newsletter');
});

it('removes members_newsletter tag when the last subscription is deleted', function () {
    $subscription = createSubscriptionForUser(
        $user = User::factory()->create()
    );

    expect(Mailcoach::getSubscriber($user->email)->tags)
        ->toContain('newsletter')
        ->toContain('members_newsletter');

    $subscription->delete();

    expect(Mailcoach::getSubscriber($user->email)->tags)
        ->toContain('newsletter')
        ->not->toContain('members_newsletter');
});

it('does not duplicate the members_newsletter tag on repeated saves', function () {
    $subscription = createSubscriptionForUser(
        $user = User::factory()->create()
    );

    $subscription->update(['quantity' => 2]);

    expect(collect(Mailcoach::getSubscriber($user->email)->tags))
        ->filter(fn ($t) => $t === 'members_newsletter')->toHaveCount(1);
});

it('removes members_newsletter when membership is no longer active', function () {
    $subscription = createSubscriptionForUser(
        $user = User::factory()->create()
    );

    expect(Mailcoach::getSubscriber($user->email)->tags)
        ->toContain('members_newsletter');

    $subscription->update([
        'stripe_status' => StripeSubscription::STATUS_CANCELED,
        'ends_at' => now(),
    ]);

    expect(Mailcoach::getSubscriber($user->email)->tags)
        ->not->toContain('members_newsletter');
});
