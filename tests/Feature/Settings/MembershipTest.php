<?php

use App\Models\User;
use Laravel\Cashier\Subscription;
use Livewire\Livewire;
use Tests\Concerns\InteractsWithStripe;

uses(InteractsWithStripe::class);

function createSubscription(User $user, array $data = []): Subscription
{
    return tap(
        $user->subscriptions()->create(array_merge([
            'type' => 'membership',
            'stripe_id' => 'sub_123',
            'stripe_status' => 'active',
            'stripe_price' => 'price_123',
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ], $data)),
        fn (Subscription $subscription) => $subscription->items()->create([
            'subscription_id' => $subscription->id,
            'stripe_id' => 'si_123',
            'stripe_product' => config('cashier.products.membership.agepac'),
            'stripe_price' => 'price_123',
            'quantity' => 1,
        ])
    );
}

beforeEach(function () {
    $this->fakeStripeMembershipProducts()->mockStripe([
        '/v1/customers' => ['object' => 'customer', 'id' => 'cus_test_foobar'],
        '/v1/billing_portal/sessions' => [
            'object' => 'billing_portal.session',
            'id' => 'bps_test_foobar',
            'url' => 'https://billing.stripe.com/p/session/test_foobar',
        ],
    ]);

    $this->user = User::factory()->asCustomer()->create();
    $this->actingAs($this->user);
});

it('renders the livewire component', function () {
    $this->get(route('settings.membership'))
        ->assertOk()
        ->assertSeeLivewire('pages::settings.membership');
});

it('computes the current user’s membership subscription', function () {
    $component = Livewire::test('pages::settings.membership');

    expect($component->get('subscription'))->toBeNull();

    $subscription = createSubscription($this->user);

    $this->user->refresh();

    expect($component->get('subscription'))
        ->toBeInstanceOf(Subscription::class)
        ->toBe($subscription);
});

it('only retrieves subscriptions of membership type', function () {
    createSubscription($this->user, ['type' => 'default']);

    Livewire::test('pages::settings.membership')
        ->assertSetStrict('subscription', null);
});

it('allows visiting the billing portal', function () {
    createSubscription($this->user);

    Livewire::test('pages::settings.membership')
        ->assertSeeText(__('settings.membership.manage-action'))
        ->call('openBillingPortal')
        ->assertRedirectContains('https://billing.stripe.com/p/session');
});

it('shows the form to create a subscription if none exist or are valid', function () {
    // Case where the user has no subscription
    Livewire::test('pages::settings.membership')
        ->assertSeeLivewire('pages::settings.create-membership-form');

    // Case where the subscription is canceled and has expired
    createSubscription($this->user, ['stripe_status' => 'canceled', 'ends_at' => now()->subHour()]);
    $this->user->refresh();

    Livewire::test('pages::settings.membership')
        ->assertSeeLivewire('pages::settings.create-membership-form');
});

it('displays appropriate callouts upon checkout return', function () {
    $this->get(route('settings.membership').'?session_id=cs_123')
        ->assertSeeText(__('settings.membership.callouts.checkout-completed.heading'));

    $this->get(route('settings.membership').'?checkout_canceled=1&session_id=cs_123')
        ->assertSeeText(__('settings.membership.callouts.checkout-interrupted.heading'));
});

it('allows resuming a canceled subscription', function () {
    $subscription = createSubscription($this->user, ['ends_at' => now()->addMonth()]);

    expect($subscription)
        ->canceled()->toBeTrue()
        ->recurring()->toBeFalse();

    // Mock the request to resume the subscription.
    $this->mockStripe([
        '/v1/subscriptions/sub_123' => [
            'object' => 'subscription',
            'id' => 'sub_test_123',
            'status' => 'active',
            'cancel_at_period_end' => false,
        ],
    ]);

    Livewire::test('pages::settings.membership')
        ->assertSeeText(__('settings.membership.callouts.no-auto-renew.heading'))
        ->assertSeeText(__('settings.membership.callouts.no-auto-renew.action'))
        ->call('resume');

    // The Stripe SDK encodes booleans as strings before they reach the HTTP client.
    expect($this->stripeRequestParams('/v1/subscriptions/sub_123'))
        ->cancel_at_period_end->toBe('false');

    expect($subscription->refresh())
        ->canceled()->toBeFalse()
        ->recurring()->toBeTrue();
});

it('shows the currently subscribed plan even if the subscription has no items', function () {
    config()->set('cashier.products.membership', [
        'agepac' => 'prod_agepac_123',
        'agepac+alumni' => 'prod_alumni_456',
    ]);

    $this->mockStripe([
        '/v1/prices/price_123' => [
            'object' => 'price',
            'id' => 'price_123',
            'product' => 'prod_agepac_123',
        ],
    ]);

    // Create an active subscription with NO items
    $this->user->subscriptions()->create([
        'type' => 'membership',
        'stripe_id' => 'sub_123',
        'stripe_status' => 'active',
        'stripe_price' => 'price_123',
        'quantity' => 1,
        'trial_ends_at' => null,
        'ends_at' => null,
    ]);

    Livewire::test('pages::settings.membership')
        ->assertSeeTextInOrder([
            __('settings.membership.callouts.subscription-active.heading'),
            __('products.membership.agepac.name'),
        ]);
});
