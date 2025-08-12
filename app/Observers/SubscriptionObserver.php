<?php

namespace App\Observers;

use ClarkeWing\LegacySync\Enums\SyncDirection;
use ClarkeWing\LegacySync\Facades\LegacySync;
use Laravel\Cashier\Subscription;

class SubscriptionObserver
{
    public function saved(Subscription $subscription): void
    {
        LegacySync::syncRecord($subscription->getTable(), $subscription->getKey(), SyncDirection::NewToLegacy);
    }
}
