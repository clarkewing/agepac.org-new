<?php

namespace App\Http\Responses\Fortify;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Http\Responses\EmailVerificationNotificationSentResponse as FortifyEmailVerificationNotificationSentResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify's response, with the moment another resend is allowed flashed so
 * the verification notice can count down to it instead of offering a
 * button that the "verification" rate limiter would refuse.
 */
class EmailVerificationNotificationSentResponse extends FortifyEmailVerificationNotificationSentResponse
{
    public const string RESEND_AVAILABLE_AT_KEY = 'verification.resend_available_at';

    /**
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        /** @var Limit $limit */
        $limit = RateLimiter::limiter(config('fortify.limiters.verification'))($request);

        $request->session()->flash(self::RESEND_AVAILABLE_AT_KEY, now()->addSeconds($limit->decaySeconds)->getTimestamp());

        return parent::toResponse($request);
    }
}
