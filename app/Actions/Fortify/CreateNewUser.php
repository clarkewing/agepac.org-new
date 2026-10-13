<?php

namespace App\Actions\Fortify;

use App\Actions\MakeUsername;
use App\Enums\ClassCourse;
use App\Enums\Gender;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Propaganistas\LaravelPhone\Rules\Phone;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function __construct(protected MakeUsername $makeUsername) {}

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        $validated = Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
            'class_course' => ['required', Rule::enum(ClassCourse::class)],
            'class_year' => ['required', Rule::date()->format('Y')],
            'gender' => ['required', Rule::enum(Gender::class)],
            'birth_date' => ['required', Rule::date()->format('Y-m-d')->after('150 years ago')->beforeOrEqual('15 years ago')],
            'phone' => ['required', (new Phone)->international()->country('FR')],
        ])->validate();

        return User::create([
            ...$validated,
            'username' => ($this->makeUsername)($validated['first_name'], $validated['last_name']),
        ]);
    }
}
