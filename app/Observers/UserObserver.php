<?php

namespace App\Observers;

use App\Actions\Mailcoach\SubscribeUserToListAction;
use App\Actions\Mailcoach\UnsubscribeUserFromListAction;
use App\Actions\Mailcoach\UpdateSubscriberEmailAction;
use App\Models\User;
use ClarkeWing\LegacySync\Enums\SyncDirection;
use ClarkeWing\LegacySync\Facades\LegacySync;

class UserObserver
{
    protected const string MAILCOACH_LIST_TAG = 'newsletter';

    public function saved(User $user): void
    {
        LegacySync::syncRecord($user->getTable(), $user->getKey(), SyncDirection::NewToLegacy);
    }

    public function created(User $user): void
    {
        app(SubscribeUserToListAction::class)($user, self::MAILCOACH_LIST_TAG);
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged('email')) {
            app(UpdateSubscriberEmailAction::class)($user);
        }
    }

    public function deleted(User $user): void
    {
        app(UnsubscribeUserFromListAction::class)($user, self::MAILCOACH_LIST_TAG);
    }
}
