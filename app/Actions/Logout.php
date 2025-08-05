<?php

namespace App\Actions;

use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): Redirector|Response
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('home');
    }
}
