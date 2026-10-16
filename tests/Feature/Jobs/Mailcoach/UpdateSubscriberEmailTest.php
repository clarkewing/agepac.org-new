<?php

use App\Jobs\Mailcoach\UpdateSubscriberEmail;
use App\Services\Mailcoach\Facades\Mailcoach;

it('moves the subscriber from the old email to the new one', function () {
    Mailcoach::subscribe('old@example.com', 'Old', 'User');

    app()->call([new UpdateSubscriberEmail('old@example.com', 'new@example.com'), 'handle']);

    expect(Mailcoach::getSubscriber('new@example.com'))->email->toBe('new@example.com');
    expect(Mailcoach::getSubscriber('old@example.com'))->toBeNull();
});

it('does nothing when no subscriber was found for the old email', function () {
    app()->call([new UpdateSubscriberEmail('old@example.com', 'new@example.com'), 'handle']);

    expect(Mailcoach::getSubscriber('old@example.com'))->toBeNull();
    expect(Mailcoach::getSubscriber('new@example.com'))->toBeNull();
});
