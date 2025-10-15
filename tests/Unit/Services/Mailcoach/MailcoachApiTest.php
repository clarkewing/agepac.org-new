<?php

use App\Services\Mailcoach\MailcoachApi;
use App\Services\Mailcoach\Subscriber;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;

beforeEach(function () {
    config()->set('services.mailcoach.url', 'https://mailcoach.test/api');
    config()->set('services.mailcoach.token', 'test-token');
    config()->set('services.mailcoach.lists.default', 'default-list-uuid');

    $this->api = new MailcoachApi;
});

function fakeSubscriber(): Subscriber
{
    return new Subscriber(
        uuid: Str::uuid(),
        email: fake()->email(),
        first_name: fake()->firstName(),
        last_name: fake()->lastName(),
        subscribedAt: now()->subDay(),
        unsubscribedAt: null,
        tags: ['foo', 'bar'],
        extra_attributes: ['zip' => 'yin']
    );
}

describe('getSubscriber', function () {
    it('hits the appropriate endpoint', function () {
        Http::fake();

        $this->api->getSubscriber('john@example.com');

        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                   Str::startsWith($request->url(), 'https://mailcoach.test/api/email-lists/default-list-uuid/subscribers') &&
                   $request->hasHeader('Authorization', 'Bearer test-token') &&
                   Uri::of($request->url())->query()->all() === ['filter' => ['email' => 'john@example.com']];

        });
    });

    it('finds and maps a subscriber', function () {
        Http::fake([
            'https://mailcoach.test/api/email-lists/default-list-uuid/subscribers*' => Http::response([
                'data' => [[
                    'uuid' => 'sub-1',
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'subscribed_at' => '2025-01-01 08:00:00',
                    'unsubscribed_at' => null,
                    'tags' => ['member'],
                    'extra_attributes' => ['level' => 'gold'],
                ]],
            ]),
        ]);

        expect($this->api->getSubscriber('john@example.com'))
            ->toBeInstanceOf(Subscriber::class)
            ->uuid->toBe('sub-1')
            ->email->toBe('john@example.com')
            ->first_name->toBe('John')
            ->last_name->toBe('Doe')
            ->subscribedAt->toEqual(Carbon::parse('2025-01-01 08:00:00'))
            ->unsubscribedAt->toBeNull()
            ->tags->toEqualCanonicalizing(['member'])
            ->extra_attributes->toEqualCanonicalizing(['level' => 'gold']);
    });

    it('returns null when no results found', function () {
        Http::fake([
            'https://mailcoach.test/api/email-lists/default-list-uuid/subscribers*' => Http::response([
                'data' => [],
            ]),
        ]);

        expect($this->api->getSubscriber('nobody@example.com'))
            ->toBeNull();
    });

    it('returns null when request fails or throws', function () {
        Http::fakeSequence('https://mailcoach.test/api/*')
            ->pushStatus(500)
            ->pushFailedConnection();

        expect($this->api->getSubscriber('john@example.com'))->toBeNull();

        expect($this->api->getSubscriber('john@example.com'))->toBeNull();
    });
});

describe('subscribe', function () {
    it('hits the appropriate endpoint', function () {
        Http::fake();

        $this->api->subscribe(
            'example@grounded-labs.co',
            'Henry',
            'Ford',
            ['foo' => 'bar'],
            skipConfirmation: true,
        );

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                   $request->url() === 'https://mailcoach.test/api/email-lists/default-list-uuid/subscribers' &&
                   $request->hasHeader('Authorization', 'Bearer test-token') &&
                   $request['email'] === 'example@grounded-labs.co' &&
                   $request['first_name'] === 'Henry' &&
                   $request['last_name'] === 'Ford' &&
                   $request['extra_attributes'] === ['foo' => 'bar'] &&
                   $request['skip_confirmation'] === true;
        });
    });

    it('returns a Subscriber DTO on success', function () {
        Http::fake(['https://mailcoach.test/api/email-lists/default-list-uuid/subscribers' => [
            'data' => [
                'uuid' => 'new-sub',
                'email' => 'new@example.com',
                'first_name' => 'New',
                'last_name' => 'User',
                'subscribed_at' => '2025-01-02 10:00:00',
                'unsubscribed_at' => null,
            ],
        ]]);

        expect($this->api->subscribe('new@example.com', 'New', 'User'))
            ->toBeInstanceOf(Subscriber::class)
            ->uuid->toBe('new-sub')
            ->email->toBe('new@example.com')
            ->first_name->toBe('New')
            ->last_name->toBe('User')
            ->subscribedAt->toEqual(Carbon::parse('2025-01-02 10:00:00'))
            ->unsubscribedAt->toBeNull()
            ->tags->toBeArray()->tobeEmpty()
            ->extra_attributes->toBeArray()->tobeEmpty();
    });

    it('returns null on failure', function () {
        Http::fake(['https://mailcoach.test/api/email-lists/default-list-uuid/subscribers' => 500]);

        expect($this->api->subscribe(''))
            ->toBeNull();
    });
});

describe('unsubscribe', function () {
    beforeEach(function () {
        $this->subscriber = fakeSubscriber();
    });

    it('hits the appropriate endpoint', function () {
        Http::fake();

        $this->api->unsubscribe($this->subscriber);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                   $request->url() === "https://mailcoach.test/api/subscribers/{$this->subscriber->uuid}/unsubscribe" &&
                   $request->hasHeader('Authorization', 'Bearer test-token');
        });
    });

    it('throws on failure', function () {
        Http::fake([
            "https://mailcoach.test/api/subscribers/{$this->subscriber->uuid}/unsubscribe" => 404,
        ]);

        expect(fn () => $this->api->unsubscribe($this->subscriber))
            ->toThrow(RequestException::class);
    });
});

describe('update', function () {
    beforeEach(function () {
        $this->subscriber = fakeSubscriber();
    });

    it('hits the appropriate endpoint', function () {
        Http::fake();

        $this->api->update($this->subscriber, [
            'first_name' => 'NewName',
            'baz' => 'qux',
        ]);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'PATCH' &&
                   $request->url() === "https://mailcoach.test/api/subscribers/{$this->subscriber->uuid}" &&
                   $request->hasHeader('Authorization', 'Bearer test-token') &&
                   $request['first_name'] === 'NewName' &&
                   $request['extra_attributes'] === ['baz' => 'qux'];
        });
    });

    it('throws on failure', function () {
        Http::fake([
            "https://mailcoach.test/api/subscribers/{$this->subscriber->uuid}" => 404,
        ]);

        expect(fn () => $this->api->update($this->subscriber, ['first_name' => 'NewName']))
            ->toThrow(RequestException::class);
    });
});

describe('addTags', function () {
    beforeEach(function () {
        $this->subscriber = fakeSubscriber();
    });

    it('hits the appropriate endpoint', function () {
        Http::fake();

        $this->api->addTags($this->subscriber, ['baz']);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'PATCH' &&
                   $request->url() === "https://mailcoach.test/api/subscribers/{$this->subscriber->uuid}" &&
                   $request->hasHeader('Authorization', 'Bearer test-token') &&
                   $request['tags'] === ['baz'] &&
                   $request['append_tags'] === true;
        });
    });

    it('throws on failure', function () {
        Http::fake([
            "https://mailcoach.test/api/subscribers/{$this->subscriber->uuid}" => 404,
        ]);

        expect(fn () => $this->api->addTags($this->subscriber, ['baz']))
            ->toThrow(RequestException::class);
    });
});

describe('removeTag', function () {
    beforeEach(function () {
        $this->subscriber = fakeSubscriber();
    });

    it('hits the appropriate endpoint', function () {
        Http::fake();

        expect($this->subscriber->tags)->toEqualCanonicalizing(['foo', 'bar']);

        $this->api->removeTag($this->subscriber, 'foo');

        Http::assertSent(function (Request $request) {
            return $request->method() === 'PATCH' &&
                   $request->url() === "https://mailcoach.test/api/subscribers/{$this->subscriber->uuid}" &&
                   $request->hasHeader('Authorization', 'Bearer test-token') &&
                   $request['tags'] === ['bar'] &&
                   $request['append_tags'] === false;
        });
    });

    it('throws on failure', function () {
        Http::fake([
            "https://mailcoach.test/api/subscribers/{$this->subscriber->uuid}" => 404,
        ]);

        expect(fn () => $this->api->removeTag($this->subscriber, 'foo'))
            ->toThrow(RequestException::class);
    });
});

describe('delete', function () {
    beforeEach(function () {
        $this->subscriber = fakeSubscriber();
    });

    it('hits the appropriate endpoint', function () {
        Http::fake();

        $this->api->delete($this->subscriber);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'DELETE' &&
                   $request->url() === "https://mailcoach.test/api/subscribers/{$this->subscriber->uuid}" &&
                   $request->hasHeader('Authorization', 'Bearer test-token');
        });
    });

    it('throws on failure', function () {
        Http::fake([
            "https://mailcoach.test/api/subscribers/{$this->subscriber->uuid}" => 404,
        ]);

        expect(fn () => $this->api->delete($this->subscriber))
            ->toThrow(RequestException::class);
    });
});

describe('rate limiting', function () {
    it('retries when a 429 is returned and eventually succeeds', function () {
        Http::fakeSequence('https://mailcoach.test/api/email-lists/default-list-uuid/subscribers*')
            ->push([], 429) // First attempt fails with Too Many Requests
            ->push([
                'data' => [[
                    'uuid' => 'sub-retry',
                    'email' => 'retry@example.com',
                    'first_name' => 'Retry',
                    'last_name' => 'User',
                    'subscribed_at' => '2025-01-01 08:00:00',
                    'unsubscribed_at' => null,
                    'tags' => [],
                    'extra_attributes' => [],
                ]],
            ]);

        expect($this->api->getSubscriber('retry@example.com'))
            ->toBeInstanceOf(Subscriber::class)
            ->uuid->toBe('sub-retry');

        // Ensure multiple attempts were made
        Http::assertSentCount(2);
    });

    it('returns fails after five retry attempts with 429 response', function () {
        Http::fake(['*' => 429]);

        expect($this->api->getSubscriber('fail@example.com'))
            ->toBeNull();

        Http::assertSentCount(6); // Original request + 5 retries
    });
});
