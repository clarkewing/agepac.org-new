<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Confirm the current user's password, locking the check out for a minute
 * after five wrong attempts, the way login is.
 */
class ConfirmPassword
{
    use PasswordValidationRules;

    protected const int MAX_ATTEMPTS = 5;

    protected const int DECAY_SECONDS = 60;

    public function __invoke(User $user, ?string $password): bool
    {
        $this->ensureIsNotRateLimited($user);

        try {
            Validator::make(['password' => $password], [
                'password' => $this->currentPasswordRules(),
            ])->validate();
        } catch (ValidationException $exception) {
            RateLimiter::hit($this->throttleKey($user), self::DECAY_SECONDS);

            throw $exception;
        }

        RateLimiter::clear($this->throttleKey($user));

        return true;
    }

    protected function ensureIsNotRateLimited(User $user): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($user), self::MAX_ATTEMPTS)) {
            return;
        }

        throw ValidationException::withMessages([
            'password' => __('auth.confirm-password.status.throttled', [
                'seconds' => RateLimiter::availableIn($this->throttleKey($user)),
            ]),
        ]);
    }

    protected function throttleKey(User $user): string
    {
        return 'password-confirmation:'.$user->getKey();
    }
}
