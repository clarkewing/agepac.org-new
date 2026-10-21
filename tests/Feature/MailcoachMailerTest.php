<?php

use Illuminate\Support\Facades\Mail;
use Spatie\MailcoachMailer\MailcoachMailerServiceProvider;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

it('sends mail through the Mailcoach transactional API of the configured instance', function () {
    config()->set('mail.mailers.mailcoach', array_merge(config('mail.mailers.mailcoach'), [
        'domain' => 'agepac.mailcoach.app',
        'token' => 'test-token',
    ]));

    $requests = [];

    app()->getProvider(MailcoachMailerServiceProvider::class)->setClient(
        new MockHttpClient(function (string $method, string $url, array $options) use (&$requests) {
            $requests[] = compact('method', 'url', 'options');

            return new MockResponse('', ['http_code' => 204]);
        }),
    );

    Mail::mailer('mailcoach')->raw('Bonjour', function ($message) {
        $message
            ->from('contact@agepac.org')
            ->to('pilote@example.com')
            ->subject('Test');
    });

    expect($requests)->toHaveCount(1)
        ->and($requests[0]['method'])->toBe('POST')
        ->and($requests[0]['url'])->toBe('https://agepac.mailcoach.app/api/transactional-mails/send')
        ->and($requests[0]['options']['normalized_headers']['authorization'])->toBe(['Authorization: Bearer test-token'])
        ->and(json_decode($requests[0]['options']['body'], true))->toMatchArray([
            'from' => 'contact@agepac.org',
            'to' => 'pilote@example.com',
            'subject' => 'Test',
        ]);
});
