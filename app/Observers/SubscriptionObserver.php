<?php

namespace App\Observers;

use App\Actions\Mailcoach\SubscribeUserToListAction;
use App\Actions\Mailcoach\UnsubscribeUserFromListAction;
use App\Enums\MailcoachTag;
use App\Models\User;
use ClarkeWing\LegacySync\Enums\SyncDirection;
use ClarkeWing\LegacySync\Facades\LegacySync;
use Laravel\Cashier\Subscription;

class SubscriptionObserver
{
    public function saved(Subscription $subscription): void
    {
        LegacySync::syncRecord($subscription->getTable(), $subscription->getKey(), SyncDirection::NewToLegacy);

        $this->syncMailcoach($subscription->user);
    }

    public function deleted(Subscription $subscription): void
    {
        LegacySync::deleteRecord($subscription->getTable(), $subscription->getKey(), SyncDirection::NewToLegacy);

        $this->syncMailcoach($subscription->user);
    }

    protected function syncMailcoach(User $user): void
    {
        $hasActiveSubscription = $user->subscriptions()->where('type', 'membership')->active()->exists();

        if ($hasActiveSubscription) {
            app(SubscribeUserToListAction::class)($user, MailcoachTag::MEMBERS_NEWSLETTER);
        } else {
            app(UnsubscribeUserFromListAction::class)($user, MailcoachTag::MEMBERS_NEWSLETTER);
        }
    }
}
