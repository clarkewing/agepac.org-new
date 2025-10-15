<?php

namespace Tests\Feature\Console;

use App\Actions\Mailcoach\SubscribeUserToListAction;
use App\Models\User;
use App\Services\Mailcoach\Facades\Mailcoach;
use Laravel\Cashier\Subscription;

function createSubscriptionForUser(User $user, array $overrides = []): Subscription
{
    return Subscription::factory()->create(array_merge([
        'user_id' => $user->id,
        'type' => 'membership',
    ], $overrides));
}

it('subscribes all users to newsletter and syncs members tag based on active subscription', function () {
    $userWithoutSubscription = User::factory()->create();

    createSubscriptionForUser(
        $userWithSubscription = User::factory()->create()
    );

    $staleUser = User::factory()->create();
    app(SubscribeUserToListAction::class)($staleUser, 'members_newsletter');

    $this->artisan('mailcoach:sync-users')
        ->assertSuccessful();

    expect(Mailcoach::getSubscriber($userWithoutSubscription->email))
        ->not->toBeNull()
        ->tags->toContain('newsletter')
        ->tags->not->toContain('members_newsletter');

    expect(Mailcoach::getSubscriber($userWithSubscription->email))
        ->not->toBeNull()
        ->tags->toContain('newsletter')
        ->tags->toContain('members_newsletter');

    expect(Mailcoach::getSubscriber($staleUser->email))
        ->not->toBeNull()
        ->tags->toContain('newsletter')
        ->tags->not->toContain('members_newsletter');
});

it('is idempotent and does not duplicate tags when run multiple times', function () {
    createSubscriptionForUser(
        $user = User::factory()->create()
    );

    $this->artisan('mailcoach:sync-users')->assertSuccessful();
    $this->artisan('mailcoach:sync-users')->assertSuccessful();

    expect(collect(Mailcoach::getSubscriber($user->email)->tags))
        ->filter(fn ($t) => $t === 'newsletter')->toHaveCount(1)
        ->filter(fn ($t) => $t === 'members_newsletter')->toHaveCount(1);
});
