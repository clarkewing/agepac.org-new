<?php

use App\Listeners\StripeEventListener;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Events\WebhookHandled;
use Laravel\Cashier\Events\WebhookReceived;

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
    })->group('stripe', 'api');

    it('does nothing if no billable user found', function () {
        User::factory()->create();

        simulateWebhook('payment_method.attached', [
            'id' => 'pm_card_visa',
            'customer' => 'cus_999',
        ]);

        $this->assertDatabaseMissing(User::class, ['pm_type' => 'visa']);
    })->group('stripe', 'api');

    it('does nothing if the user already has a default payment method', function () {
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
    })->group('stripe', 'api');
});
