<?php

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Mailgun\Transport\MailgunHttpTransport;

it('sends mail through the Mailgun API for the configured domain and region', function () {
    config()->set('services.mailgun', [
        'domain' => 'mg.agepac.org',
        'secret' => 'key-test',
        'endpoint' => 'api.eu.mailgun.net',
        'scheme' => 'https',
    ]);

    $transport = Mail::mailer('mailgun')->getSymfonyTransport();

    expect($transport)->toBeInstanceOf(MailgunHttpTransport::class)
        ->and((string) $transport)->toBe('mailgun+https://api.eu.mailgun.net?domain=mg.agepac.org');
});
