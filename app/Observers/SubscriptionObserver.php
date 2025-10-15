<?php

namespace App\Observers;

use App\Actions\Mailcoach\SubscribeUserToListAction;
use App\Actions\Mailcoach\UnsubscribeUserFromListAction;
use App\Models\User;
use ClarkeWing\LegacySync\Enums\SyncDirection;
use ClarkeWing\LegacySync\Facades\LegacySync;
use Laravel\Cashier\Subscription;

class SubscriptionObserver
{
    protected const string MAILCOACH_LIST_TAG = 'members_newsletter';

    public function saved(Subscription $subscription): void
    {
        LegacySync::syncRecord($subscription->getTable(), $subscription->getKey(), SyncDirection::NewToLegacy);

        $this->syncMailcoach($subscription->user);
    }

    public function deleted(Subscription $subscription): void
    {
        $this->syncMailcoach($subscription->user);
    }

    protected function syncMailcoach(User $user): void
    {
        $hasActiveSubscription = $user->subscriptions()->where('type', 'membership')->active()->exists();

        if ($hasActiveSubscription) {
            app(SubscribeUserToListAction::class)($user, self::MAILCOACH_LIST_TAG);
        } else {
            app(UnsubscribeUserFromListAction::class)($user, self::MAILCOACH_LIST_TAG);
        }
    }
}
