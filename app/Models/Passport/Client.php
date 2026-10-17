<?php

namespace App\Models\Passport;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Passport\Client as BaseClient;
use Laravel\Passport\Scope;

class Client extends BaseClient
{
    /**
     * Our own apps sign members in without a consent prompt, as long as they
     * may use them. Otherwise, the authorization view explains why not.
     *
     * @param  Scope[]  $scopes
     */
    public function skipsAuthorization(Authenticatable $user, array $scopes): bool
    {
        return $this->firstParty()
            && $user instanceof User
            && $user->canSignInToApps();
    }
}
