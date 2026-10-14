<?php

use App\Services\Mailcoach\MailcoachApi;
use App\Services\Mailcoach\Testing\Fakes\MailcoachApiFake;

beforeEach(function () {
    app()->forgetInstance(MailcoachApi::class);
});

it('falls back to the fake when Mailcoach is unconfigured outside production', function () {
    config()->set('services.mailcoach.url', null);

    expect(app(MailcoachApi::class))->toBeInstanceOf(MailcoachApiFake::class);
});

it('uses the real client when Mailcoach is configured', function () {
    config()->set('services.mailcoach.url', 'https://mailcoach.test/api');
    config()->set('services.mailcoach.token', 'test-token');
    config()->set('services.mailcoach.lists.default', 'default-list-uuid');

    expect(app(MailcoachApi::class))
        ->toBeInstanceOf(MailcoachApi::class)
        ->not->toBeInstanceOf(MailcoachApiFake::class);
});

it('refuses to run unconfigured in production', function () {
    app()->detectEnvironment(fn (): string => 'production');
    config()->set('services.mailcoach.url', null);

    app(MailcoachApi::class);
})->throws(LogicException::class, 'Mailcoach API URL not configured.');
