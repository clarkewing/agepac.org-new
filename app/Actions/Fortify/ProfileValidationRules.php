<?php

namespace App\Actions\Fortify;

use App\Enums\ClassCourse;
use App\Enums\Gender;
use App\Models\User;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

/**
 * Validation rules for the fields a member fills in when registering or
 * recovering an account, shared by the Fortify action and the wizard steps.
 */
trait ProfileValidationRules
{
    /**
     * @param  User|null  $user  The member whose own email address should not count as taken.
     * @return array<string, array<int, mixed>>
     */
    protected function identityRules(?User $user = null): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->when($user)->ignore($user),
            ],
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function classRules(): array
    {
        return [
            'class_course' => ['required', Rule::enum(ClassCourse::class)],
            'class_year' => ['required', Rule::date()->format('Y')],
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function detailsRules(): array
    {
        return [
            'gender' => ['required', Rule::enum(Gender::class)],
            'birth_date' => ['required', Rule::date()->format('Y-m-d')->after('150 years ago')->beforeOrEqual('15 years ago')],
            'phone' => ['required', (new Phone)->international()->country('FR')],
        ];
    }
}
