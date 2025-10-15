<?php

namespace App\Services\Mailcoach\Testing\Fakes;

use App\Services\Mailcoach\MailcoachApi;
use App\Services\Mailcoach\Subscriber;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MailcoachApiFake extends MailcoachApi
{
    /**
     * @var Collection<string, Subscriber>
     */
    protected Collection $subscribers;

    public function __construct()
    {
        $this->subscribers = collect();
    }

    /** Utility for assertions in tests */
    public function allSubscribers(): Collection
    {
        return $this->subscribers->values();
    }

    public function getSubscriber(string $email, ?string $listUuid = null): ?Subscriber
    {
        return $this->subscribers->get($this->subscriberKey($email, $listUuid));
    }

    public function subscribe(
        string $email,
        ?string $first_name = null,
        ?string $last_name = null,
        ?array $extra_attributes = null,
        ?string $listUuid = null,
        bool $skipConfirmation = false,
        bool $skipWelcomeMail = false,
    ): ?Subscriber {
        return tap(new Subscriber(
            uuid: Str::uuid()->toString(),
            email: $email,
            first_name: $first_name,
            last_name: $last_name,
            subscribedAt: now(),
            unsubscribedAt: null,
            tags: [],
            extra_attributes: $extra_attributes ?? [],
        ), fn ($subscriber) => $this->subscribers->put($this->subscriberKey($email, $listUuid), $subscriber));
    }

    public function unsubscribe(Subscriber $subscriber): void
    {
        $this->updateSubscriber($subscriber, function (Subscriber $storedSubscriber) {
            $storedSubscriber->unsubscribedAt = now();
        });
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

        $this->updateSubscriber($subscriber, function (Subscriber $storedSubscriber) use ($attributes) {
            $storedSubscriber->email = $attributes['email'] ?? $storedSubscriber->email;
            $storedSubscriber->first_name = $attributes['first_name'] ?? $storedSubscriber->first_name;
            $storedSubscriber->last_name = $attributes['last_name'] ?? $storedSubscriber->last_name;
            $storedSubscriber->tags = $attributes['tags'] ?? $storedSubscriber->tags;
            $storedSubscriber->extra_attributes = $attributes['extra_attributes'] ??
                                                  $storedSubscriber->extra_attributes;
        });
    }

    public function addTags(Subscriber $subscriber, array $tags): void
    {
        $this->updateSubscriber($subscriber, function (Subscriber $storedSubscriber) use ($tags) {
            $storedSubscriber->tags = collect($storedSubscriber->tags)->concat($tags)->unique()->values()->all();
        });
    }

    public function removeTag(Subscriber $subscriber, string $tag): void
    {
        $this->updateSubscriber($subscriber, function (Subscriber $storedSubscriber) use ($tag) {
            $storedSubscriber->tags = collect($storedSubscriber->tags)->reject($tag)->values()->all();
        });
    }

    public function delete(Subscriber $subscriber): void
    {
        $this->subscribers = $this->subscribers
            ->reject(fn (Subscriber $storedSubscriber) => $storedSubscriber->uuid === $subscriber->uuid);
    }

    protected function subscriberKey(string $email, ?string $listUuid): string
    {
        $listUuid ??= config('services.mailcoach.lists.default');

        return "{$listUuid}::".strtolower($email);
    }

    protected function updateSubscriber(Subscriber $targetSubscriber, callable $mutator): void
    {
        $key = $this->subscribers->search(
            fn (Subscriber $storedSubscriber) => $storedSubscriber->uuid === $targetSubscriber->uuid
        );

        if ($key === false) {
            return;
        }

        $subscriber = $this->subscribers->get($key);

        $mutator($subscriber);

        $this->subscribers->put($key, $subscriber);
    }
}
