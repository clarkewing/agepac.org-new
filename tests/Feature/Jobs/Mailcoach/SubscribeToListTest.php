<?php

use App\Enums\MailcoachTag;
use App\Jobs\Mailcoach\SubscribeToList;
use App\Models\User;
use App\Services\Mailcoach\Facades\Mailcoach;

it('creates a new subscriber and adds the provided tag when not found', function () {
    $user = User::factory()->make([
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
        'email' => 'ada@example.com',
        'class_course' => 'EPL',
        'class_year' => '1843',
    ]);

    app()->call([new SubscribeToList($user, MailcoachTag::NEWSLETTER), 'handle']);

    expect(Mailcoach::getSubscriber('ada@example.com'))
        ->not->toBeNull()
        ->email->toBe('ada@example.com')
        ->first_name->toBe('Ada')
        ->last_name->toBe('Lovelace')
        ->extra_attributes->toBe([
            'class_course' => 'EPL',
            'class_year' => '1843',
        ])
        ->tags->toContain('newsletter');
});

it('adds the tag to an existing subscriber', function () {
    Mailcoach::subscribe('alan@example.com');

    app()->call([new SubscribeToList(User::factory()->make(['email' => 'alan@example.com']), MailcoachTag::NEWSLETTER), 'handle']);

    expect(Mailcoach::getSubscriber('alan@example.com'))
        ->tags->toContain('newsletter');
});
