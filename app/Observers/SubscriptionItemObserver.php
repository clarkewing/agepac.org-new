<?php

namespace App\Observers;

use ClarkeWing\LegacySync\Enums\SyncDirection;
use ClarkeWing\LegacySync\Facades\LegacySync;
use Laravel\Cashier\SubscriptionItem;

class SubscriptionItemObserver
{
    public function saved(SubscriptionItem $item): void
    {
        LegacySync::syncRecord($item->getTable(), $item->getKey(), SyncDirection::NewToLegacy);
    }
}
