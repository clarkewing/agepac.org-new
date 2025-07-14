<?php

use App\Enums\Products\Membership as MembershipProduct;
use App\Livewire\Settings\CreateMembershipForm;
use App\Models\User;
use Illuminate\Validation\Rules\Enum as EnumRule;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('shows the available subscriptions', function () {
    Livewire::test(CreateMembershipForm::class)
        ->assertSeeTextInOrder(array_map(
            fn (MembershipProduct $case) => $case->label(),
            MembershipProduct::cases(),
        ));
});

it('creates a checkout session and redirects to it', function () {
    Livewire::test(CreateMembershipForm::class)
        ->set('selectedMembership', MembershipProduct::AGEPAC)
        ->call('checkout')
        ->assertRedirectContains('https://checkout.stripe.com/c/pay');
});

describe('validation', function () {
    it('requires selectedMembership', function () {
        Livewire::test(CreateMembershipForm::class)
            ->call('checkout')
            ->assertHasErrors(['selectedMembership' => 'required']);
    });

    it('rejects invalid selectedMembership values', function () {
        Livewire::test(CreateMembershipForm::class)
            ->set('selectedMembership', 'invalid')
            ->call('checkout')
            ->assertHasErrors(['selectedMembership' => EnumRule::class]);
    });
});
