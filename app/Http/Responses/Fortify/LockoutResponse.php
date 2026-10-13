<?php

namespace App\Http\Responses\Fortify;

use App\Services\Auth\ProgressiveLoginRateLimiter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Http\Responses\LockoutResponse as FortifyLockoutResponse;

/**
 * Fortify's response, except that the message carries its own key (the
 * login form shows it above the form rather than on the email field) and
 * the moment the lockout ends is flashed so the form can count down.
 */
class LockoutResponse extends FortifyLockoutResponse
{
    public const string LOCKOUT_UNTIL_KEY = 'login.lockout_until';

    public function __construct(ProgressiveLoginRateLimiter $limiter)
    {
        parent::__construct($limiter);
    }

    /**
     * @param  Request  $request
     */
    public function toResponse($request): never
    {
        $seconds = $this->limiter->availableIn($request);

        $request->session()->flash(self::LOCKOUT_UNTIL_KEY, $this->limiter->availableAt($request));

        throw ValidationException::withMessages([
            'throttle' => [__('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ])->status(429);
    }
}
