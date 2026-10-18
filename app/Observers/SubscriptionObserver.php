<?php

namespace App\Observers;

use App\Jobs\Mailcoach\UpdateMembersNewsletterSubscription;
use ClarkeWing\LegacySync\Enums\SyncDirection;
use ClarkeWing\LegacySync\Facades\LegacySync;
use Laravel\Cashier\Subscription;

class SubscriptionObserver
{
    public function saved(Subscription $subscription): void
    {
        LegacySync::syncRecord($subscription->getTable(), $subscription->getKey(), SyncDirection::NewToLegacy);

        UpdateMembersNewsletterSubscription::dispatch($subscription->user);

        $subscription->user->revokeAppTokensIfIneligible();
    }

    public function deleted(Subscription $subscription): void
    {
        LegacySync::deleteRecord($subscription->getTable(), $subscription->getKey(), SyncDirection::NewToLegacy);

        UpdateMembersNewsletterSubscription::dispatch($subscription->user);

        $subscription->user->revokeAppTokensIfIneligible();
    }
}
