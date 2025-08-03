<?php

namespace App\Observers;

use ClarkeWing\LegacySync\Actions\SyncRecord;
use ClarkeWing\LegacySync\Enums\SyncDirection;
use Laravel\Cashier\Subscription;

class SubscriptionObserver
{
    public function saved(Subscription $subscription): void
    {
        $action = app(SyncRecord::class);

        $action->handle($subscription->getTable(), $subscription->getKey(), SyncDirection::NewToLegacy);
    }
}
