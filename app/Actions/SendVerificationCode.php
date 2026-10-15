<?php

namespace App\Actions;

use App\Models\User;
use App\Notifications\VerificationCode;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Email a member a one-time code, at most one a minute.
 */
class SendVerificationCode
{
    public const int EXPIRES_IN_MINUTES = 10;

    protected const int RESEND_DELAY_SECONDS = 60;

    public function __invoke(User $user): void
    {
        if ($availableAt = $this->availableAt($user)) {
            throw new TooManyRequestsHttpException($availableAt - now()->getTimestamp());
        }

        RateLimiter::hit($this->throttleKey($user), self::RESEND_DELAY_SECONDS);

        $user->notify(new VerificationCode($user->createOneTimePassword(self::EXPIRES_IN_MINUTES)));
    }

    /**
     * Whether the member holds a code that has not expired.
     */
    public function isPending(User $user): bool
    {
        return $user->oneTimePasswords()->where('expires_at', '>', now())->exists();
    }

    /**
     * The timestamp from which another code may be sent, or null if one may
     * be sent now.
     */
    public function availableAt(User $user): ?int
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($user), 1)) {
            return null;
        }

        return now()->addSeconds(RateLimiter::availableIn($this->throttleKey($user)))->getTimestamp();
    }

    protected function throttleKey(User $user): string
    {
        return 'verification-code:'.$user->getKey();
    }
}
