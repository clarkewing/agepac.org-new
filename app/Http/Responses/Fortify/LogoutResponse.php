<?php

namespace App\Http\Responses\Fortify;

use Illuminate\Http\Request;
use Laravel\Fortify\Http\Responses\LogoutResponse as FortifyLogoutResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify's response, except that members land on the public site, which
 * lives on another domain than the member area.
 */
class LogoutResponse extends FortifyLogoutResponse
{
    /**
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        return $request->wantsJson()
            ? parent::toResponse($request)
            : redirect()->route('public.home');
    }
}
