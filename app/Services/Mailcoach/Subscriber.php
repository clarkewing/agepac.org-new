<?php

namespace App\Services\Mailcoach;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;

class Subscriber
{
    public function __construct(
        public string $uuid,
        public string $email,
        public ?string $first_name,
        public ?string $last_name,
        public ?CarbonInterface $subscribedAt,
        public ?CarbonInterface $unsubscribedAt,
        public array $tags = [],
        public array $extra_attributes = [],
    ) {}

    public static function fromResponse(array $response): static
    {
        return new self(
            uuid: $response['uuid'],
            email: $response['email'],
            first_name: $response['first_name'],
            last_name: $response['last_name'],
            subscribedAt: ! is_null($response['subscribed_at'])
                ? Date::parse($response['subscribed_at'])
                : null,
            unsubscribedAt: ! is_null($response['unsubscribed_at'])
                ? Date::parse($response['unsubscribed_at'])
                : null,
            tags: $response['tags'] ?? [],
            extra_attributes: $response['extra_attributes'] ?? [],
        );
    }
}
