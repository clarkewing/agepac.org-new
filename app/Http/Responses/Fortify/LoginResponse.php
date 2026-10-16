<?php

namespace App\Http\Responses\Fortify;

use ClarkeWing\Handoff\HandoffIntended;
use Illuminate\Http\Request;
use Laravel\Fortify\Http\Responses\LoginResponse as FortifyLoginResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify's response, except that a member the legacy app sent here to log
 * in is handed back to the page they wanted there.
 */
class LoginResponse extends FortifyLoginResponse
{
    /**
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        if (! $request->wantsJson() && $handoffUrl = resolve(HandoffIntended::class)->pullUrl($request->user())) {
            return redirect()->away($handoffUrl);
        }

        return parent::toResponse($request);
    }
}
