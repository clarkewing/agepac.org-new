<?php

namespace App\Observers;

use App\Models\User;
use ClarkeWing\LegacySync\Enums\SyncDirection;
use ClarkeWing\LegacySync\Facades\LegacySync;

class UserObserver
{
    public function saved(User $user): void
    {
        LegacySync::syncRecord($user->getTable(), $user->getKey(), SyncDirection::NewToLegacy);
    }
}
