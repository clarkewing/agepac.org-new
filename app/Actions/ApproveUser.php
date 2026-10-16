<?php

namespace App\Actions;

use App\Models\User;
use App\Notifications\AccountApproved;

class ApproveUser
{
    /**
     * Give a member access to the members area and let them know.
     */
    public function __invoke(User $user): void
    {
        $user->approve();

        $user->notify(new AccountApproved);
    }
}
