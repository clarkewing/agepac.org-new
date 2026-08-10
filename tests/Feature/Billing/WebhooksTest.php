<?php

use App\Listeners\StripeEventListener;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Events\WebhookHandled;
use Laravel\Cashier\Events\WebhookReceived;
use Tests\Concerns\InteractsWithStripe;

uses(InteractsWithStripe::class);

function simulateWebhook(string $type, array $data = []): void
{
    $payload = [
        'type' => $type,
        'data' => ['object' => $data],
    ];

    resolve(StripeEventListener::class)
        ->handle(new WebhookReceived($payload));
}

beforeEach(function () {
    config()->set('cashier.webhook.secret', null);

    Event::fake();
});

it('dispatches an event when a webhook is received', function () {
    $this->postJson(route('cashier.webhook'), ['type' => 'foo']);

    Event::assertDispatched(WebhookReceived::class);
});

it('does nothing if the webhook is not handled', function () {
    simulateWebhook('invalid.webhook');

    Event::assertNotDispatched(WebhookHandled::class);
});

describe('payment_method.attached', function () {
    it('sets a default payment method', function () {
        $this->mockStripe([
            '/v1/customers*' => [
                'id' => 'cus_test_foobar',
                'object' => 'customer',
                'invoice_settings' => ['default_payment_method' => null],
            ],
            '/v1/payment_methods/pm_card_visa*' => $this->paymentMethodResponse('pm_card_visa', 'visa'),
        ]);

        $user = User::factory()->asCustomer()->create();

        $paymentMethod = $user->addPaymentMethod('pm_card_visa');

        expect($user->fresh())
            ->hasDefaultPaymentMethod()->toBeFalse();

        simulateWebhook('payment_method.attached', [
            'id' => $paymentMethod->id,
            'customer' => $user->stripe_id,
        ]);

        expect($user->fresh())
            ->hasDefaultPaymentMethod()->toBeTrue()
            ->pm_type->toBe('visa');
    });

    it('does nothing if no billable user found', function () {
        User::factory()->create();

        simulateWebhook('payment_method.attached', [
            'id' => 'pm_card_visa',
            'customer' => 'cus_999',
        ]);

        $this->assertDatabaseMissing(User::class, ['pm_type' => 'visa']);
    });

    it('does nothing if the user already has a default payment method', function () {
        $this->mockStripe([
            '/v1/customers*' => [
                'id' => 'cus_test_foobar',
                'object' => 'customer',
                'invoice_settings' => ['default_payment_method' => null],
            ],
            '/v1/payment_methods/pm_card_visa*' => $this->paymentMethodResponse('pm_card_visa', 'visa'),
            '/v1/payment_methods/pm_card_mastercard*' => $this->paymentMethodResponse('pm_card_mastercard', 'mastercard'),
        ]);

        $user = User::factory()->asCustomer()->create();

        $user->updateDefaultPaymentMethod('pm_card_visa');

        expect($user->fresh())
            ->hasDefaultPaymentMethod()->toBeTrue()
            ->pm_type->toBe('visa');

        $newPaymentMethod = $user->addPaymentMethod('pm_card_mastercard');

        simulateWebhook('payment_method.attached', [
            'id' => $newPaymentMethod->id,
            'customer' => $user->stripe_id,
        ]);

        expect($user->fresh())
            ->hasDefaultPaymentMethod()->toBeTrue()
            ->pm_type->toBe('visa');
    });
});

describe('price.updated', function () {
    beforeEach(function () {
        config()->set('cashier.products.membership', [
            'agepac' => 'prod_agepac_123',
        ]);
    });

    afterEach(function () {
        Cache::flush();
    });

    it('repopulates cache with updated price data', function () {
        $cacheKey = 'membership.prod_agepac_123.price';

        Cache::forever($cacheKey, (object) ['id' => 'price_old']);
        expect(Cache::get($cacheKey)->id)->toBe('price_old');

        $this->mockStripe([
            '/v1/products/prod_agepac_123' => $this->productResponse('prod_agepac_123', 'price_new'),
        ]);

        simulateWebhook('price.updated', [
            'id' => 'price_new',
            'product' => 'prod_agepac_123',
        ]);

        expect(Cache::get($cacheKey)->id)->toBe('price_new');

        Event::assertDispatched(WebhookHandled::class);
    });

    it('handles webhook when no cache exists', function () {
        $cacheKey = 'membership.prod_agepac_123.price';

        expect(Cache::has($cacheKey))->toBeFalse();

        $this->mockStripe([
            '/v1/products/prod_agepac_123' => $this->productResponse('prod_agepac_123', 'price_123'),
        ]);

        simulateWebhook('price.updated', [
            'id' => 'price_123',
            'product' => 'prod_agepac_123',
        ]);

        expect(Cache::get($cacheKey)->id)->toBe('price_123');

        Event::assertDispatched(WebhookHandled::class);
    });
});

describe('product.updated', function () {
    beforeEach(function () {
        config()->set('cashier.products.membership', [
            'agepac' => 'prod_agepac_123',
        ]);
    });

    afterEach(function () {
        Cache::flush();
    });

    it('repopulates cache with updated default price', function () {
        $cacheKey = 'membership.prod_agepac_123.price';

        Cache::forever($cacheKey, (object) ['id' => 'price_old']);
        expect(Cache::get($cacheKey)->id)->toBe('price_old');

        $this->mockStripe([
            '/v1/products/prod_agepac_123' => $this->productResponse('prod_agepac_123', 'price_new'),
        ]);

        simulateWebhook('product.updated', [
            'id' => 'prod_agepac_123',
            'default_price' => 'price_new',
        ]);

        expect(Cache::get($cacheKey)->id)->toBe('price_new');

        Event::assertDispatched(WebhookHandled::class);
    });

    it('handles webhook when no cache exists', function () {
        $cacheKey = 'membership.prod_agepac_123.price';

        expect(Cache::has($cacheKey))->toBeFalse();

        $this->mockStripe([
            '/v1/products/prod_agepac_123' => $this->productResponse('prod_agepac_123', 'price_456'),
        ]);

        simulateWebhook('product.updated', [
            'id' => 'prod_agepac_123',
            'default_price' => 'price_456',
        ]);

        expect(Cache::get($cacheKey)->id)->toBe('price_456');

        Event::assertDispatched(WebhookHandled::class);
    });
});
