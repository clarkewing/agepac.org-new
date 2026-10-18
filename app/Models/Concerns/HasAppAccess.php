<?php

namespace App\Models\Concerns;

use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;

/**
 * Signing in to AGEPAC's apps (perfos, escales) through agepac.org, which
 * acts as their OAuth server.
 */
trait HasAppAccess
{
    use HasApiTokens;

    /**
     * Whether the member may sign in to the apps: verified, approved and paying.
     */
    public function canSignInToApps(): bool
    {
        return $this->hasVerifiedEmail()
            && $this->isApproved()
            && $this->subscribed('membership');
    }

    /**
     * Sign the member out of the apps once they may no longer use them,
     * rather than waiting for their access token to expire.
     */
    public function revokeAppTokensIfIneligible(): void
    {
        if ($this->canSignInToApps()) {
            return;
        }

        $this->tokens()->each(function (Token $token): void {
            $token->revoke();
            $token->refreshToken?->revoke();
        });
    }
}
