<?php

use App\Actions\Mailcoach\UnsubscribeUserFromListAction;
use App\Models\User;
use App\Services\Mailcoach\Facades\Mailcoach;

beforeEach(function () {
    Mailcoach::fake();
});

it('does nothing when no subscriber exists', function () {
    $user = User::factory()->make(['email' => 'ghost@example.com']);

    app(UnsubscribeUserFromListAction::class)($user, 'newsletter');

    expect(Mailcoach::getSubscriber('ghost@example.com'))->toBeNull();
});

it('unsubscribes when the only tag matches', function () {
    $user = User::factory()->make(['email' => 'solo@example.com']);

    $subscriber = Mailcoach::subscribe('solo@example.com');
    Mailcoach::addTags($subscriber, ['newsletter']);

    expect(Mailcoach::getSubscriber('solo@example.com'))
        ->tags->toBe(['newsletter'])
        ->unsubscribedAt->toBeNull();

    app(UnsubscribeUserFromListAction::class)($user, 'newsletter');

    expect(Mailcoach::getSubscriber('solo@example.com'))
        ->tags->toBeArray()->toBeEmpty()
        ->unsubscribedAt->not->toBeNull();
});

it('removes only the provided tag when multiple tags exist', function () {
    $user = User::factory()->make(['email' => 'multi@example.com']);

    $subscriber = Mailcoach::subscribe('multi@example.com');
    Mailcoach::addTags($subscriber, ['newsletter', 'members_newsletter']);

    expect(Mailcoach::getSubscriber('multi@example.com'))
        ->tags->toBe(['newsletter', 'members_newsletter'])
        ->unsubscribedAt->toBeNull();

    app(UnsubscribeUserFromListAction::class)($user, 'members_newsletter');

    expect(Mailcoach::getSubscriber('multi@example.com'))
        ->tags->toEqualCanonicalizing(['newsletter'])
        ->unsubscribedAt->toBeNull();
});
