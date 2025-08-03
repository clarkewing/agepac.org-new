<?php

namespace App\Observers;

use ClarkeWing\LegacySync\Actions\SyncRecord;
use ClarkeWing\LegacySync\Enums\SyncDirection;
use Laravel\Cashier\SubscriptionItem;

class SubscriptionItemObserver
{
    public function saved(SubscriptionItem $item): void
    {
        $action = app(SyncRecord::class);

        $action->handle($item->getTable(), $item->getKey(), SyncDirection::NewToLegacy);
    }
}
