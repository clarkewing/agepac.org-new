<?php

use App\Services\Mailcoach\Facades\Mailcoach;
use App\Services\Mailcoach\MailcoachApi;
use App\Services\Mailcoach\Subscriber;
use App\Services\Mailcoach\Testing\Fakes\MailcoachApiFake;

beforeEach(function () {
    config()->set('services.mailcoach.lists.default', 'default-list-uuid');
});

it('Facade::fake swaps implementation and isFake returns true', function () {
    expect(Mailcoach::getFacadeRoot())->toBeInstanceOf(MailcoachApi::class)
        ->and(Mailcoach::isFake())->toBeFalse();

    $fake = Mailcoach::fake();

    expect(Mailcoach::getFacadeRoot())->toBeInstanceOf(MailcoachApiFake::class)
        ->and(Mailcoach::isFake())->toBeTrue();

    expect($fake->allSubscribers())->toHaveCount(0);
});

it('can subscribe and retrieve a subscriber using the fake', function () {
    $fake = Mailcoach::fake();

    expect($subscriber = $fake->subscribe('a@example.com', 'A', 'User'))
        ->toBeInstanceOf(Subscriber::class);

    expect($fake->getSubscriber('a@example.com'))
        ->uuid->toBe($subscriber->uuid);
});

it('can update, tag, untag, unsubscribe and delete using the fake', function () {
    $fake = Mailcoach::fake();

    $subscriber = $fake->subscribe('b@example.com', 'B', 'User', ['x' => 1]);

    // Update first name and extra attributes
    $fake->update($subscriber, [
        'first_name' => 'Bee',
        'extra_attributes' => ['y' => 2],
    ]);
    expect($fake->getSubscriber('b@example.com'))
        ->first_name->toBe('Bee')
        ->extra_attributes->toBe(['y' => 2]);

    // Add tags
    $fake->addTags($subscriber, ['alpha', 'beta']);
    expect($fake->getSubscriber('b@example.com'))
        ->tags->toEqualCanonicalizing(['alpha', 'beta']);

    // Remove a tag
    $fake->removeTag($subscriber, 'alpha');
    expect($fake->getSubscriber('b@example.com'))
        ->tags->toEqualCanonicalizing(['beta']);

    // Unsubscribe
    expect($subscriber->unsubscribedAt)->toBeNull();
    $fake->unsubscribe($subscriber);
    expect($fake->getSubscriber('b@example.com'))
        ->unsubscribedAt->not->toBeNull();

    // Delete
    $fake->delete($subscriber);
    expect($fake->getSubscriber('b@example.com'))
        ->toBeNull();
});

it('differentiates subscribers between lists', function () {
    $fake = Mailcoach::fake();

    $defaultListSuscriber = $fake->subscribe('test@example.com');
    $otherListSuscriber = $fake->subscribe('test@example.com', listUuid: 'other-list-uuid');

    expect($fake->getSubscriber('test@example.com'))
        ->uuid->toBe($defaultListSuscriber->uuid);

    expect($fake->getSubscriber('test@example.com', listUuid: 'other-list-uuid'))
        ->uuid->toBe($otherListSuscriber->uuid);
});
