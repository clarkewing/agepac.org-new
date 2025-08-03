<?php

namespace App\Observers;

use App\Models\User;
use ClarkeWing\LegacySync\Actions\SyncRecord;
use ClarkeWing\LegacySync\Enums\SyncDirection;

class UserObserver
{
    public function saved(User $user): void
    {
        app(SyncRecord::class)
            ->handle($user->getTable(), $user->getKey(), SyncDirection::NewToLegacy);
    }
}
