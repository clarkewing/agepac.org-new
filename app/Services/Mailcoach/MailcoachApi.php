<?php

namespace App\Services\Mailcoach;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class MailcoachApi
{
    public function getSubscriber(string $email, ?string $listUuid = null): ?Subscriber
    {
        $listUuid ??= config('services.mailcoach.lists.default');

        try {
            $response = Http::timeout(10)->withToken(config('services.mailcoach.token'))
                ->get(config('services.mailcoach.url')."/email-lists/{$listUuid}/subscribers", [
                    'filter' => [
                        'email' => $email,
                    ],
                ]);
        } catch (Exception $e) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $subscribers = $response->json('data');

        if (! isset($subscribers[0])) {
            return null;
        }

        return Subscriber::fromResponse($subscribers[0]);
    }

    public function subscribe(
        string $email,
        ?string $first_name = null,
        ?string $last_name = null,
        ?array $extra_attributes = null,
        ?string $listUuid = null,
        bool $skipConfirmation = false,
    ): ?Subscriber {
        $listUuid ??= config('services.mailcoach.lists.default');

        $response = Http::timeout(10)->withToken(config('services.mailcoach.token'))
            ->post(config('services.mailcoach.url')."/email-lists/{$listUuid}/subscribers", [
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'extra_attributes' => $extra_attributes,
                'skip_confirmation' => $skipConfirmation,
            ]);

        if (! $response->successful() || ! $response->json('data') || ! $response->json('data.uuid')) {
            return null;
        }

        return Subscriber::fromResponse($response->json('data'));
    }

    public function unsubscribe(Subscriber $subscriber): void
    {
        Http::timeout(10)->withToken(config('services.mailcoach.token'))
            ->post(config('services.mailcoach.url')."/subscribers/{$subscriber->uuid}/unsubscribe")
            ->throw();
    }

    public function update(Subscriber $subscriber, array $attributes): void
    {
        $standardAttributes = [
            'email',
            'first_name',
            'last_name',
            'tags',
            'append_tags',
            'extra_attributes',
        ];

        $extraAttributes = $attributes['extra_attributes'] ?? Arr::except($attributes, $standardAttributes);

        $attributes = Arr::only($attributes, $standardAttributes);
        $attributes['extra_attributes'] = $extraAttributes;

        Http::timeout(10)->withToken(config('services.mailcoach.token'))
            ->patch(config('services.mailcoach.url')."/subscribers/{$subscriber->uuid}", $attributes)
            ->throw();
    }

    public function addTags(Subscriber $subscriber, array $tags): void
    {
        Http::timeout(10)->withToken(config('services.mailcoach.token'))
            ->patch(config('services.mailcoach.url')."/subscribers/{$subscriber->uuid}", [
                'tags' => array_values($tags),
                'append_tags' => true,
            ])
            ->throw();
    }

    public function removeTag(Subscriber $subscriber, string $tag): void
    {
        $tags = array_filter($subscriber->tags, fn (string $existingTag) => $existingTag !== $tag);

        Http::timeout(10)->withToken(config('services.mailcoach.token'))
            ->patch(config('services.mailcoach.url')."/subscribers/{$subscriber->uuid}", [
                'tags' => array_values($tags),
                'append_tags' => false,
            ])
            ->throw();
    }

    public function delete(Subscriber $subscriber): void
    {
        Http::timeout(10)->withToken(config('services.mailcoach.token'))
            ->delete(config('services.mailcoach.url')."/subscribers/{$subscriber->uuid}")
            ->throw();
    }
}
