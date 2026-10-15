<?php

namespace App\Actions;

use App\Http\Middleware\RedirectIfRecoveryRequired;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Log in a member whose account has just been recovered, discarding the
 * recovery's session state first.
 */
class CompleteRecovery
{
    public function __invoke(User $user): void
    {
        Session::forget([RedirectIfRecoveryRequired::SESSION_KEY, 'email-verified']);

        Auth::login($user);

        Session::regenerate();
    }
}
