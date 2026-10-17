<?php

namespace App\Observers;

use App\Enums\MailcoachTag;
use App\Jobs\Mailcoach\UnsubscribeFromList;
use App\Jobs\Mailcoach\UpdateSubscriberEmail;
use App\Models\User;
use ClarkeWing\LegacySync\Enums\SyncDirection;
use ClarkeWing\LegacySync\Facades\LegacySync;

class UserObserver
{
    public function saved(User $user): void
    {
        LegacySync::syncRecord($user->getTable(), $user->getKey(), SyncDirection::NewToLegacy);
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged('email')) {
            UpdateSubscriberEmail::dispatch($user->getOriginal('email'), $user->email);
        }

        if ($user->wasChanged(['email_verified_at', 'approved_at'])) {
            $user->revokeAppTokensIfIneligible();
        }
    }

    public function deleted(User $user): void
    {
        LegacySync::deleteRecord($user->getTable(), $user->getKey(), SyncDirection::NewToLegacy);

        UnsubscribeFromList::dispatch($user->email, MailcoachTag::NEWSLETTER);
    }
}
