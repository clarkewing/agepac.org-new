<?php

use App\Livewire\Settings\CreateMembershipForm;
use App\Livewire\Settings\Membership;
use App\Models\User;
use Laravel\Cashier\Subscription;
use Livewire\Livewire;

pest()->group('stripe', 'api');

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

beforeEach(function () {
    $this->user = User::factory()->asCustomer()->create();
    $this->actingAs($this->user);
});

it('renders the livewire component', function () {
    $this->get(route('settings.membership'))
        ->assertSeeLivewire(Membership::class);
});

it('computes the current user’s membership subscription', function () {
    $component = Livewire::test(Membership::class);

    expect($component->get('subscription'))->toBeNull();

    $subscription = createSubscription($this->user);

    $this->user->refresh();

    expect($component->get('subscription'))
        ->toBeInstanceOf(Subscription::class)
        ->id->toBe($subscription->id);
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
    Livewire::test(Membership::class)
        ->assertSeeLivewire(CreateMembershipForm::class);

    createSubscription($this->user, ['stripe_status' => 'canceled', 'ends_at' => now()->subHour()]);

    // Ensure the user's subscriptions are refreshed
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
