<?php

use App\Enums\Products\Membership as MembershipEnum;
use App\Livewire\Settings\CreateMembershipForm;
use App\Livewire\Settings\Membership;
use App\Models\User;
use Laravel\Cashier\Subscription;
use Livewire\Livewire;
use Tests\Helpers\StripeHelpers;

pest()->group('stripe', 'api');

beforeEach(function () {
    $this->user = User::factory()->asCustomer()->create();
    $this->actingAs($this->user);
});

afterEach(function () {
    StripeHelpers::cleanup();
});

it('renders the livewire component', function () {
    $this->get(route('settings.membership'))
        ->assertOk()
        ->assertSeeLivewire(Membership::class);
});

it('computes the current user’s membership subscription', function () {
    $component = Livewire::test(Membership::class);

    expect($component->get('subscription'))->toBeNull();

    $subscription = createSubscription($this->user);

    $this->user->refresh();

    expect($component->get('subscription'))
        ->toBeInstanceOf(Subscription::class)
        ->toBe($subscription);
});

it('only retrieves subscriptions of membership type', function () {
    createSubscription($this->user, ['type' => 'default']);

    Livewire::test(Membership::class)
        ->assertSetStrict('subscription', null);
});

it('allows visiting the billing portal', function () {
    createSubscription($this->user);

    Livewire::test(Membership::class)
        ->assertSeeText(__('settings.membership.manage-action'))
        ->call('openBillingPortal')
        ->assertRedirectContains('https://billing.stripe.com/p/session');
});

it('shows the form to create a subscription if none exist or are valid', function () {
    // Case where the user has no subscription
    Livewire::test(Membership::class)
        ->assertSeeLivewire(CreateMembershipForm::class);

    // Case where the subscription is canceled and has expired
    createSubscription($this->user, ['stripe_status' => 'canceled', 'ends_at' => now()->subHour()]);
    $this->user->refresh();

    Livewire::test(Membership::class)
        ->assertSeeLivewire(CreateMembershipForm::class);
});

it('displays appropriate callouts upon checkout return', function () {
    $this->get(route('settings.membership').'?session_id=cs_123')
        ->assertSeeText(__('settings.membership.callouts.checkout-completed.heading'));

    $this->get(route('settings.membership').'?checkout_canceled=1&session_id=cs_123')
        ->assertSeeText(__('settings.membership.callouts.checkout-interrupted.heading'));
});

it('allows resuming a canceled subscription', function () {
    $subscription = tap(createSubscription($this->user, fake: false))->cancel();

    expect($subscription->canceled())->toBeTrue();

    Livewire::test(Membership::class)
        ->assertSeeText(__('settings.membership.callouts.no-auto-renew.heading'))
        ->assertSeeText(__('settings.membership.callouts.no-auto-renew.action'))
        ->call('resume');

    expect($subscription->refresh()->recurring())->toBeTrue();
});

it('shows the currently subscribed plan even if the subscription has no items', function () {
    config()->set('cashier.products.membership', [
        'agepac' => 'prod_agepac_123',
        'agepac+alumni' => 'prod_alumni_456',
    ]);

    StripeHelpers::mockStripeClientWithResponse(
        StripeHelpers::stripePriceResponse('price_123', 'prod_agepac_123')
    );

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

    Livewire::test(Membership::class)
        ->assertSeeTextInOrder([
            __('settings.membership.callouts.subscription-active.heading'),
            __('products.membership.agepac.name'),
        ]);
});

function createSubscription(User $user, array $data = [], bool $fake = true): Subscription
{
    if ($fake) {
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
            function (Subscription $subscription) {
                $subscription->items()->create([
                    'subscription_id' => $subscription->id,
                    'stripe_id' => 'si_123',
                    'stripe_product' => config('cashier.products.membership.agepac'),
                    'stripe_price' => 'price_123',
                    'quantity' => 1,
                ]);
            }
        );
    }

    return $user
        ->newSubscription('membership', MembershipEnum::AGEPAC->stripePrice()->id)
        ->create('pm_card_visa');
}
