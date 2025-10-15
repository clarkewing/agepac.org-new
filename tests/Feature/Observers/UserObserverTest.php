<?php

use App\Models\User;
use App\Services\Mailcoach\Facades\Mailcoach;

it('subscribes a newly created user to the newsletter list', function () {
    $user = User::factory()->create();

    expect(Mailcoach::getSubscriber($user->email))
        ->not->toBeNull()
        ->tags->toContain('newsletter');
});

it('updates the subscriber email when the user email changes', function () {
    $user = User::factory()->create(['email' => 'old@example.com']);

    expect(Mailcoach::getSubscriber('old@example.com'))->not->toBeNull();

    $user->update(['email' => 'new@example.com']);

    expect(Mailcoach::getSubscriber('old@example.com'))->toBeNull();
    expect(Mailcoach::getSubscriber('new@example.com'))->not->toBeNull();
});

it('unsubscribes the user from the newsletter list on delete', function () {
    $user = User::factory()->create();

    // Sanity: subscribed and has newsletter tag from create observer
    expect(Mailcoach::getSubscriber($user->email))
        ->not->toBeNull()
        ->tags->toContain('newsletter')
        ->unsubscribedAt->toBeNull();

    $user->delete();

    expect(Mailcoach::getSubscriber($user->email))
        ->not->toBeNull()
        ->tags->not->toContain('newsletter')
        ->unsubscribedAt->not->toBeNull();
});
