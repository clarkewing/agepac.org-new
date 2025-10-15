<?php

use App\Actions\Mailcoach\UnsubscribeUserFromListAction;
use App\Models\User;
use App\Services\Mailcoach\MailcoachApi;
use App\Services\Mailcoach\Testing\Fakes\MailcoachApiFake;

beforeEach(function () {
    $this->mailcoach = new MailcoachApiFake;
    $this->app->instance(MailcoachApi::class, $this->mailcoach);
});

it('does nothing when no subscriber exists', function () {
    $user = User::factory()->make(['email' => 'ghost@example.com']);

    app(UnsubscribeUserFromListAction::class)($user, 'newsletter');

    expect($this->mailcoach->getSubscriber('ghost@example.com'))->toBeNull();
});

it('unsubscribes when the only tag matches', function () {
    $user = User::factory()->make(['email' => 'solo@example.com']);

    $subscriber = $this->mailcoach->subscribe('solo@example.com');
    $this->mailcoach->addTags($subscriber, ['newsletter']);

    expect($this->mailcoach->getSubscriber('solo@example.com'))
        ->tags->toBe(['newsletter'])
        ->unsubscribedAt->toBeNull();

    app(UnsubscribeUserFromListAction::class)($user, 'newsletter');

    expect($this->mailcoach->getSubscriber('solo@example.com'))
        ->tags->toBeArray()->toBeEmpty()
        ->unsubscribedAt->not->toBeNull();
});

it('removes only the provided tag when multiple tags exist', function () {
    $user = User::factory()->make(['email' => 'multi@example.com']);

    $subscriber = $this->mailcoach->subscribe('multi@example.com');
    $this->mailcoach->addTags($subscriber, ['newsletter', 'members_newsletter']);

    expect($this->mailcoach->getSubscriber('multi@example.com'))
        ->tags->toBe(['newsletter', 'members_newsletter'])
        ->unsubscribedAt->toBeNull();

    app(UnsubscribeUserFromListAction::class)($user, 'members_newsletter');

    expect($this->mailcoach->getSubscriber('multi@example.com'))
        ->tags->toEqualCanonicalizing(['newsletter'])
        ->unsubscribedAt->toBeNull();
});
