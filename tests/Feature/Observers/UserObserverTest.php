<?php

use App\Models\User;
use App\Services\Mailcoach\MailcoachApi;
use App\Services\Mailcoach\Testing\Fakes\MailcoachApiFake;

beforeEach(function () {
    $this->mailcoach = new MailcoachApiFake;
    $this->app->instance(MailcoachApi::class, $this->mailcoach);
});

it('subscribes a newly created user to the newsletter list', function () {
    $user = User::factory()->create();

    expect($this->mailcoach->getSubscriber($user->email))
        ->not->toBeNull()
        ->tags->toContain('newsletter');
});

it('updates the subscriber email when the user email changes', function () {
    $user = User::factory()->create(['email' => 'old@example.com']);

    expect($this->mailcoach->getSubscriber('old@example.com'))->not->toBeNull();

    $user->update(['email' => 'new@example.com']);

    expect($this->mailcoach->getSubscriber('old@example.com'))->toBeNull();
    expect($this->mailcoach->getSubscriber('new@example.com'))->not->toBeNull();
});

it('unsubscribes the user from the newsletter list on delete', function () {
    $user = User::factory()->create();

    // Sanity: subscribed and has newsletter tag from create observer
    expect($this->mailcoach->getSubscriber($user->email))
        ->not->toBeNull()
        ->tags->toContain('newsletter')
        ->unsubscribedAt->toBeNull();

    $user->delete();

    expect($this->mailcoach->getSubscriber($user->email))
        ->not->toBeNull()
        ->tags->not->toContain('newsletter')
        ->unsubscribedAt->not->toBeNull();
});
