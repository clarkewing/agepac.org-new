<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Validation\Rules\Enum as EnumRule;
use Livewire\Livewire;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = Livewire::test(Register::class)
        ->set('first_name', 'Test')
        ->set('last_name', 'User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('class_course', 'EPL/S')
        ->set('class_year', '2015')
        ->set('gender', 'M')
        ->set('birth_date', '1994-09-22')
        ->set('phone', '+33612345678')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('updating the phone number reformats it', function () {
    Livewire::test(Register::class)
        ->set('phone', '+33612345678')
        ->assertSet('phone', '+33 6 12 34 56 78');

    Livewire::test(Register::class)
        ->set('phone', '0612345678')
        ->assertSet('phone', '+33 6 12 34 56 78');

    Livewire::test(Register::class)
        ->set('phone', '+3312345')
        ->assertSet('phone', '+33 12345');

    // Leaves invalid local number as is
    Livewire::test(Register::class)
        ->set('phone', '12345')
        ->assertSet('phone', '12345');
});

describe('validation', function () {
    test('fails when required fields are missing', function () {
        Livewire::test(Register::class)
            ->set('first_name', '')
            ->set('last_name', '')
            ->set('email', '')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->set('class_course', '')
            ->set('class_year', '')
            ->set('gender', '')
            ->set('birth_date', '')
            ->set('phone', '')
            ->call('register')
            ->assertHasErrors([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required',
                'password' => 'required',
                'class_course' => 'required',
                'class_year' => 'required',
                'gender' => 'required',
                'birth_date' => 'required',
                'phone' => 'required',
            ]);
    });

    test('fails with invalid email format', function () {
        Livewire::test(Register::class)
            ->set('email', 'invalid-email')
            ->call('register')
            ->assertHasErrors(['email' => 'email']);
    });

    test('fails with password confirmation mismatch', function () {
        Livewire::test(Register::class)
            ->set('password', 'password')
            ->set('password_confirmation', 'different-password')
            ->call('register')
            ->assertHasErrors(['password' => 'confirmed']);
    });

    test('fails with invalid class course', function () {
        Livewire::test(Register::class)
            ->set('class_course', 'Invalid-Course')
            ->call('register')
            ->assertHasErrors(['class_course' => EnumRule::class]);
    });

    test('fails with invalid class year format', function () {
        Livewire::test(Register::class)
            ->set('class_year', 'not-a-year')
            ->call('register')
            ->assertHasErrors(['class_year' => 'date_format']);
    });

    test('fails with invalid gender', function () {
        Livewire::test(Register::class)
            ->set('gender', 'X')
            ->call('register')
            ->assertHasErrors(['gender' => EnumRule::class]);
    });

    test('fails with invalid birth date format', function () {
        Livewire::test(Register::class)
            ->set('birth_date', 'not-a-date')
            ->call('register')
            ->assertHasErrors(['birth_date' => 'date_format']);
    });

    test('fails with birth date too far in the past', function () {
        Livewire::test(Register::class)
            ->set('birth_date', '1800-01-01')
            ->call('register')
            ->assertHasErrors(['birth_date' => 'after']);
    });

    test('fails with birth date too recent', function () {
        // Get a date 10 years ago (too recent, according to validation rules)
        $recentDate = now()->subYears(10)->format('Y-m-d');

        Livewire::test(Register::class)
            ->set('birth_date', $recentDate)
            ->call('register')
            ->assertHasErrors(['birth_date' => 'before_or_equal']);
    });

    test('fails with invalid phone number', function () {
        Livewire::test(Register::class)
            ->set('phone', 'not-a-phone')
            ->call('register')
            ->assertHasErrors(['phone']);
    });

    test('fails with duplicate email', function () {
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        Livewire::test(Register::class)
            ->set('email', 'duplicate@example.com')
            ->call('register')
            ->assertHasErrors(['email' => 'unique']);
    });
});
