<?php

use App\Actions\Mailcoach\SubscribeUserToListAction;
use App\Models\User;
use App\Services\Mailcoach\Facades\Mailcoach;

beforeEach(function () {
    Mailcoach::fake();
});

it('creates a new subscriber and adds the provided tag when not found', function () {
    $user = User::factory()->make([
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
        'email' => 'ada@example.com',
        'class_course' => 'EPL',
        'class_year' => '1843',
    ]);

    expect(Mailcoach::getSubscriber('ada@example.com'))->toBeNull();

    app(SubscribeUserToListAction::class)($user, 'newsletter');

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
    $user = User::factory()->make([
        'email' => 'alan@example.com',
    ]);

    expect(Mailcoach::subscribe('alan@example.com'))
        ->tags->toBeArray()->toBeEmpty();

    app(SubscribeUserToListAction::class)($user, 'newsletter');

    expect(Mailcoach::getSubscriber('alan@example.com'))
        ->not->toBeNull()
        ->tags->toContain('newsletter');
});
