<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create(['password' => 'password']);

    $this->actingAs($this->user);
});

it('renders the password page', function () {
    $this->get(route('settings.password'))
        ->assertSuccessful()
        ->assertSeeLivewire('pages::settings.password');
});

it('updates the password', function () {
    Livewire::test('pages::settings.password')
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasNoErrors()
        ->assertDispatched('toast-show', fn (string $name, array $params): bool => $params['slots']['text'] === __('settings.password.status.updated'));

    expect(Hash::check('new-password', $this->user->refresh()->password))->toBeTrue();
});

it('requires the current password to update the password', function () {
    Livewire::test('pages::settings.password')
        ->set('current_password', 'wrong-password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);

    expect(Hash::check('password', $this->user->refresh()->password))->toBeTrue();
});
