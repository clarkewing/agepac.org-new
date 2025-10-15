<?php

use App\Actions\Mailcoach\UpdateSubscriberEmailAction;
use App\Models\User;
use App\Services\Mailcoach\Facades\Mailcoach;

beforeEach(function () {
    Mailcoach::fake();
});

it('updates the subscriber email using the user’s original email as the lookup', function () {
    // Subscriber exists with the old email
    Mailcoach::subscribe('old@example.com', 'Old', 'User');

    $user = User::factory()->make([
        'email' => 'old@example.com',
        'first_name' => 'Old',
        'last_name' => 'User',
    ]);

    $user->syncOriginal();
    $user->email = 'new@example.com';

    app(UpdateSubscriberEmailAction::class)($user);

    expect(Mailcoach::getSubscriber('new@example.com'))
        ->not->toBeNull()
        ->email->toBe('new@example.com');

    expect(Mailcoach::getSubscriber('old@example.com'))->toBeNull();
});

it('does nothing when no subscriber was found for the original email', function () {
    $user = User::factory()->make(['email' => 'old@example.com']);
    $user->syncOriginal();
    $user->email = 'new@example.com';

    app(UpdateSubscriberEmailAction::class)($user);

    expect(Mailcoach::getSubscriber('wasold@example.com'))->toBeNull();
    expect(Mailcoach::getSubscriber('brandnew@example.com'))->toBeNull();
});
