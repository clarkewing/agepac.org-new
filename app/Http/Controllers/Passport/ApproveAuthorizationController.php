<?php

namespace App\Http\Controllers\Passport;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\ApproveAuthorizationController as BaseApproveAuthorizationController;
use Laravel\Passport\Http\Controllers\DenyAuthorizationController;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ApproveAuthorizationController extends BaseApproveAuthorizationController
{
    /**
     * Deny, rather than approve, a member who may not use our apps. The
     * authorization view only offers them the way back, but Passport would
     * otherwise accept that form's auth token here all the same.
     */
    public function approve(Request $request, ResponseInterface $psrResponse): Response
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->canSignInToApps()) {
            return app(DenyAuthorizationController::class)->deny($request, $psrResponse);
        }

        return parent::approve($request, $psrResponse);
    }
}
