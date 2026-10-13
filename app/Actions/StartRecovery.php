<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Session;

/**
 * Open a recovery for a user whose password is a sentinel. The wizard at
 * route('recovery') reads this session state, emails a one-time code and,
 * once the code is verified, lets the member set a password and log in.
 */
class StartRecovery
{
    public const string SESSION_KEY = 'recovery';

    public function __invoke(User $user): void
    {
        Session::put(self::SESSION_KEY, ['user_id' => $user->getKey()]);
    }
}
