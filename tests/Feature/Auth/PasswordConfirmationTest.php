<?php

use App\Models\User;

it('renders the confirm password screen', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('password.confirm'))->assertOk();
});

it('confirms the password', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('password.confirm.store'), ['password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('auth.password_confirmed_at')
        ->assertRedirect(route('dashboard', absolute: false));
});

it('rejects a wrong password', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('password.confirm.store'), ['password' => 'wrong-password'])
        ->assertSessionHasErrors(['password' => __('The provided password was incorrect.')])
        ->assertSessionMissing('auth.password_confirmed_at');
});

