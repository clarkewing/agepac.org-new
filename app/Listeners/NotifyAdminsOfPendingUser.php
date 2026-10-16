<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\UserPendingApproval;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfPendingUser
{
    /**
     * Let everyone who can approve accounts know that a new one is waiting.
     */
    public function handle(Registered $event): void
    {
        if (! $event->user instanceof User || $event->user->isApproved()) {
            return;
        }

        Notification::send(
            User::withPermission('users:approve')->get(),
            new UserPendingApproval($event->user),
        );
    }
}
