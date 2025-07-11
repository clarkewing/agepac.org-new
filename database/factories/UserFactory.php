<?php

namespace Database\Factories;

use App\Actions\MakeUsername;
use App\Enums\ClassCourse;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'username' => fn (array $attributes) => (new MakeUsername)(
                $attributes['first_name'],
                $attributes['last_name'],
            ),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'class_course' => fake()->randomElement(ClassCourse::options()),
            'class_year' => fake()->year,
            'gender' => fake()->randomElement(array_keys(Gender::options())),
            'birthdate' => fake()->date('Y-m-d', today()->subYears(18)), // At least 18 years old
            'phone' => fake()->randomElement([ // Use predefined numbers for testing as Faker can generate some weirdos
                '0669696969',
                '07 68 12 34 56',
                '06.12.34.56.78',
                '+44 7375 123456',
                '+1-202-555-5555',
            ]),
            'approved_at' => now(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved_at' => null,
        ]);
    }
}
