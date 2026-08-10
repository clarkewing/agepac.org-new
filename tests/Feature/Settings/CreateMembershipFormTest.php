<?php

use App\Enums\Products\Membership as MembershipProduct;
use App\Models\User;
use Illuminate\Validation\Rules\Enum as EnumRule;
use Livewire\Livewire;
use Tests\Concerns\InteractsWithStripe;

uses(InteractsWithStripe::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->fakeStripeMembershipProducts()->mockStripe([
        '/v1/customers' => [
            'object' => 'customer',
            'id' => 'cus_test_foobar',
        ],
        '/v1/checkout/sessions' => [
            'object' => 'checkout.session',
            'id' => 'cs_test_foobar',
            'status' => 'open',
            'url' => 'https://checkout.stripe.com/c/pay/cs_test_foobar',
        ],
    ]);
});

it('shows the available subscriptions', function () {
    Livewire::test('pages::settings.create-membership-form')
        ->assertSeeTextInOrder(array_map(
            fn (MembershipProduct $case) => $case->label(),
            MembershipProduct::cases(),
        ));
});

it('creates a checkout session and redirects to it', function () {
    Livewire::test('pages::settings.create-membership-form')
        ->set('selectedMembership', MembershipProduct::AGEPAC)
        ->call('checkout')
        ->assertRedirectContains('https://checkout.stripe.com/c/pay');
});

describe('validation', function () {
    it('requires selectedMembership', function () {
        Livewire::test('pages::settings.create-membership-form')
            ->call('checkout')
            ->assertHasErrors(['selectedMembership' => 'required']);
    });

    it('rejects invalid selectedMembership values', function () {
        Livewire::test('pages::settings.create-membership-form')
            ->set('selectedMembership', 'invalid')
            ->call('checkout')
            ->assertHasErrors(['selectedMembership' => EnumRule::class]);
    });
});
