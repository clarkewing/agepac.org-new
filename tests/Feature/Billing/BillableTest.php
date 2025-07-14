<?php

use App\Models\User;
use App\Services\Stripe\Billable;
use Propaganistas\LaravelPhone\PhoneNumber;

test('user model uses billable trait', function () {
    $user = new User;

    expect($user)->toBeInstanceOf(User::class)
        ->and(in_array(Billable::class, class_uses_recursive($user)))->toBeTrue();
});

test('billable trait provides full name', function () {
    /** @var Billable $user */
    $user = User::factory()->make([
        'first_name' => 'John',
        'last_name' => 'Appleseed',
    ]);

    expect($user->name)->toBe('John Appleseed')
        ->and($user->stripeName())->toBe('John Appleseed');
});

test('billable trait provides phone in international format', function () {
    /** @var Billable $user */
    $user = User::factory()->make([
        'phone' => '0612345678',
    ]);

    expect($user->phone)->toBeInstanceOf(PhoneNumber::class)
        ->and($user->stripePhone())->toBe('+33 6 12 34 56 78');
});

test('billable trait provides class in metadata', function () {
    /** @var Billable $user */
    $user = User::factory()->make([
        'class_course' => 'EPL/S',
        'class_year' => '2015',
    ]);

    expect($user->class)->toBe('EPL/S 2015')
        ->and($user->stripeMetadata())->toBe(['class' => 'EPL/S 2015']);
});
