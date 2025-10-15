<?php

use App\Actions\Mailcoach\UpdateSubscriberEmailAction;
use App\Models\User;
use App\Services\Mailcoach\MailcoachApi;
use App\Services\Mailcoach\Testing\Fakes\MailcoachApiFake;

beforeEach(function () {
    $this->mailcoach = new MailcoachApiFake;
    $this->app->instance(MailcoachApi::class, $this->mailcoach);
});

it('updates the subscriber email using the user’s original email as the lookup', function () {
    // Subscriber exists with the old email
    $this->mailcoach->subscribe('old@example.com', 'Old', 'User');

    $user = User::factory()->make([
        'email' => 'old@example.com',
        'first_name' => 'Old',
        'last_name' => 'User',
    ]);

    $user->syncOriginal();
    $user->email = 'new@example.com';

    app(UpdateSubscriberEmailAction::class)($user);

    expect($this->mailcoach->getSubscriber('new@example.com'))
        ->not->toBeNull()
        ->email->toBe('new@example.com');

    expect($this->mailcoach->getSubscriber('old@example.com'))->toBeNull();
});

it('does nothing when no subscriber was found for the original email', function () {
    $user = User::factory()->make(['email' => 'old@example.com']);
    $user->syncOriginal();
    $user->email = 'new@example.com';

    app(UpdateSubscriberEmailAction::class)($user);

    expect($this->mailcoach->getSubscriber('wasold@example.com'))->toBeNull();
    expect($this->mailcoach->getSubscriber('brandnew@example.com'))->toBeNull();
});
