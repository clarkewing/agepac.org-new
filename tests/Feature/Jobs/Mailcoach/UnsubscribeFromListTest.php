<?php

use App\Enums\MailcoachTag;
use App\Jobs\Mailcoach\UnsubscribeFromList;
use App\Services\Mailcoach\Facades\Mailcoach;

it('does nothing when no subscriber exists', function () {
    app()->call([new UnsubscribeFromList('ghost@example.com', MailcoachTag::NEWSLETTER), 'handle']);

    expect(Mailcoach::getSubscriber('ghost@example.com'))->toBeNull();
});

it('unsubscribes when the only tag matches', function () {
    Mailcoach::addTags(Mailcoach::subscribe('solo@example.com'), ['newsletter']);

    app()->call([new UnsubscribeFromList('solo@example.com', MailcoachTag::NEWSLETTER), 'handle']);

    expect(Mailcoach::getSubscriber('solo@example.com'))
        ->tags->toBeArray()->toBeEmpty()
        ->unsubscribedAt->not->toBeNull();
});

it('removes only the provided tag when multiple tags exist', function () {
    Mailcoach::addTags(Mailcoach::subscribe('multi@example.com'), ['newsletter', 'members_newsletter']);

    app()->call([new UnsubscribeFromList('multi@example.com', MailcoachTag::MEMBERS_NEWSLETTER), 'handle']);

    expect(Mailcoach::getSubscriber('multi@example.com'))
        ->tags->toEqualCanonicalizing(['newsletter'])
        ->unsubscribedAt->toBeNull();
});
