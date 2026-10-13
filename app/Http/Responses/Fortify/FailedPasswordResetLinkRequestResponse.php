<?php

namespace App\Http\Responses\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use Laravel\Fortify\Http\Responses\FailedPasswordResetLinkRequestResponse as FortifyFailedPasswordResetLinkRequestResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify's response, except that an unknown address gets the same answer
 * as a known one, so the form cannot be used to find out which emails
 * belong to members.
 */
class FailedPasswordResetLinkRequestResponse extends FortifyFailedPasswordResetLinkRequestResponse
{
    /**
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        if ($this->status === Password::INVALID_USER) {
            return app(SuccessfulPasswordResetLinkRequestResponse::class, ['status' => Password::RESET_LINK_SENT])
                ->toResponse($request);
        }

        return parent::toResponse($request);
    }
}
