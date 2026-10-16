<?php

use App\Jobs\Mailcoach\UpdateMembersNewsletterSubscription;
use App\Models\User;
use App\Services\Mailcoach\Facades\Mailcoach;
use Laravel\Cashier\Subscription;
use Stripe\Subscription as StripeSubscription;

it('applies the membership as it stands when the job runs', function () {
    $user = User::factory()->create();
    $subscription = Subscription::factory()->create(['user_id' => $user->id, 'type' => 'membership']);
    $job = new UpdateMembersNewsletterSubscription($user);

    expect(Mailcoach::getSubscriber($user->email)->tags)->toContain('members_newsletter');

    $subscription->updateQuietly(['stripe_status' => StripeSubscription::STATUS_CANCELED, 'ends_at' => now()]);

    app()->call([$job, 'handle']);

    expect(Mailcoach::getSubscriber($user->email)->tags)->not->toContain('members_newsletter');
});
