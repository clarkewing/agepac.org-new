<?php

use App\Actions\Mailcoach\SubscribeUserToListAction;
use App\Models\User;
use App\Services\Mailcoach\MailcoachApi;
use App\Services\Mailcoach\Testing\Fakes\MailcoachApiFake;

beforeEach(function () {
    $this->mailcoach = new MailcoachApiFake;
    $this->app->instance(MailcoachApi::class, $this->mailcoach);
});

it('creates a new subscriber and adds the provided tag when not found', function () {
    $user = User::factory()->make([
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
        'email' => 'ada@example.com',
        'class_course' => 'EPL',
        'class_year' => '1843',
    ]);

    expect($this->mailcoach->getSubscriber('ada@example.com'))->toBeNull();

    app(SubscribeUserToListAction::class)($user, 'newsletter');

    expect($this->mailcoach->getSubscriber('ada@example.com'))
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

    expect($this->mailcoach->subscribe('alan@example.com'))
        ->tags->toBeArray()->toBeEmpty();

    app(SubscribeUserToListAction::class)($user, 'newsletter');

    expect($this->mailcoach->getSubscriber('alan@example.com'))
        ->not->toBeNull()
        ->tags->toContain('newsletter');
});
