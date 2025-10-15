<?php

use App\Services\Mailcoach\Subscriber;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

it('parses a subscriber response into a value object', function () {
    Date::setTestNow('2025-01-15 12:00:00');

    expect(Subscriber::fromResponse([
        'uuid' => 'sub-123',
        'email' => 'john@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'subscribed_at' => '2025-01-01 08:30:00',
        'unsubscribed_at' => '2025-01-10 18:00:00',
        'tags' => ['member', 'news'],
        'extra_attributes' => ['foo' => 'bar'],
    ]))
        ->uuid->toBe('sub-123')
        ->email->toBe('john@example.com')
        ->first_name->toBe('John')
        ->last_name->toBe('Doe')
        ->subscribedAt->toEqual(Carbon::parse('2025-01-01 08:30:00'))
        ->unsubscribedAt->toEqual(Carbon::parse('2025-01-10 18:00:00'))
        ->tags->toBe(['member', 'news'])
        ->extra_attributes->toBe(['foo' => 'bar']);
});

it('handles null dates and missing optional arrays', function () {
    expect(Subscriber::fromResponse([
        'uuid' => 'sub-456',
        'email' => 'jane@example.com',
        'first_name' => null,
        'last_name' => null,
        'subscribed_at' => null,
        'unsubscribed_at' => null,
        // tags and extra_attributes omitted intentionally
    ]))
        ->first_name->toBeNull()
        ->last_name->toBeNull()
        ->subscribedAt->toBeNull()
        ->unsubscribedAt->toBeNull()
        ->tags->toBeArray()->toBeEmpty()
        ->extra_attributes->toBeArray()->toBeEmpty();
});
