<?php

namespace App\Observers;

use App\Actions\Mailcoach\SubscribeUserToListAction;
use App\Actions\Mailcoach\UnsubscribeUserFromListAction;
use App\Actions\Mailcoach\UpdateSubscriberEmailAction;
use App\Enums\MailcoachTag;
use App\Models\User;
use ClarkeWing\LegacySync\Enums\SyncDirection;
use ClarkeWing\LegacySync\Facades\LegacySync;

class UserObserver
{
    public function saved(User $user): void
    {
        LegacySync::syncRecord($user->getTable(), $user->getKey(), SyncDirection::NewToLegacy);
    }

    public function created(User $user): void
    {
        app(SubscribeUserToListAction::class)($user, MailcoachTag::NEWSLETTER);
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged('email')) {
            app(UpdateSubscriberEmailAction::class)($user);
        }
    }

    public function deleted(User $user): void
    {
        LegacySync::deleteRecord($user->getTable(), $user->getKey(), SyncDirection::NewToLegacy);

        app(UnsubscribeUserFromListAction::class)($user, MailcoachTag::NEWSLETTER);
    }
}
