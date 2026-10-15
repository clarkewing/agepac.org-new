<?php

namespace App\Actions\Fortify;

use App\Actions\MakeUsername;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    use ProfileValidationRules;

    public function __construct(protected MakeUsername $makeUsername) {}

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        $validated = Validator::make($input, [
            ...$this->identityRules(),
            'password' => $this->passwordRules(),
            ...$this->classRules(),
            ...$this->detailsRules(),
        ])->validate();

        return User::create([
            ...$validated,
            'username' => ($this->makeUsername)($validated['first_name'], $validated['last_name']),
        ]);
    }
}
