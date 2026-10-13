<?php

namespace App\Http\Responses\Fortify;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Http\Responses\PasswordResetResponse as FortifyPasswordResetResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify's response, with the email carried over so the login form it
 * lands on is prefilled.
 */
class PasswordResetResponse extends FortifyPasswordResetResponse
{
    /**
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        $response = parent::toResponse($request);

        return $response instanceof RedirectResponse
            ? $response->withInput($request->only('email'))
            : $response;
    }
}
