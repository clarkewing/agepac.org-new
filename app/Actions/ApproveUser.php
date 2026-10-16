<?php

namespace App\Actions;

use App\Jobs\SubscribeUserToNewsletter;
use App\Models\User;
use App\Notifications\AccountApproved;

class ApproveUser
{
    /**
     * Give a member access to the members area, let them know, and sign them
     * up to the newsletter.
     */
    public function __invoke(User $user): void
    {
        $user->approve();

        $user->notify(new AccountApproved);

        SubscribeUserToNewsletter::dispatch($user);
    }
}
