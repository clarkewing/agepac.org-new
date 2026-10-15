<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * The recovery of an account whose stored password is a sentinel, kept in
 * the session: RedirectIfRecoveryRequired starts it, the recovery pages read
 * it, and complete() saves the account and logs the member in.
 */
class AccountRecovery
{
    public const string SESSION_KEY = 'recovery';

    public static function start(User $user): void
    {
        Session::put(self::SESSION_KEY, ['user_id' => $user->getKey()]);
    }

    /**
     * The member recovering their account, if any.
     */
    public static function user(): ?User
    {
        return User::query()->find(Session::get(self::SESSION_KEY.'.user_id'));
    }

    /**
     * Whether the member recovering has verified their email address with
     * the auth.verification-code component.
     */
    public static function isVerified(): bool
    {
        $user = static::user();

        return $user !== null
            && Session::get('email-verified.user_id') === $user->getKey();
    }

    public static function cancel(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Save the recovered account, verifying its email address if it had not
     * been, then discard the recovery and log the member in.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function complete(User $user, array $attributes): void
    {
        $user
            ->fill($attributes)
            ->forceFill([
                'remember_token' => null,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])
            ->save();

        Session::forget([self::SESSION_KEY, 'email-verified']);

        Auth::login($user);

        Session::regenerate();
    }
}
