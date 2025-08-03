<?php

use App\Models\User;
use Tests\Helpers\LegacySyncHelpers as Helpers;

beforeEach(function () {
    Helpers::setupLegacyDatabase();
});

test('user is synced to legacy database when saved', function () {
    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'birth_date' => '1990-01-01',
        'gender' => 'M',
    ]);

    Helpers::verifyLegacySync('users', $user->id, [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'birthdate' => '1990-01-01',  // Mapped from birth_date to birthdate
        'gender' => 'M',
    ]);
});

test('user updates are synced to legacy database', function () {
    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    $user->update([
        'first_name' => 'Robin',
        'last_name' => 'Banks',
        'email' => 'robin.banks@example.com',
    ]);

    Helpers::verifyLegacySync('users', $user->id, [
        'first_name' => 'Robin',
        'last_name' => 'Banks',
        'email' => 'robin.banks@example.com',
    ]);
});

test('user payment method details are synced to legacy database', function () {
    $user = User::factory()->create([
        'pm_type' => 'visa',
        'pm_last_four' => '4242',
    ]);

    Helpers::verifyLegacySync('users', $user->id, [
        'card_brand' => 'visa', // Mapped from pm_type
        'card_last_four' => '4242', // Mapped from pm_last_four
    ]);
});
