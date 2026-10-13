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
        ->assertSessionHasErrors(['password' => __('validation.current_password')])
        ->assertSessionMissing('auth.password_confirmed_at');
});

it('locks out password confirmation after five wrong attempts', function () {
    $this->freezeTime();
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->actingAs($user)
            ->post(route('password.confirm.store'), ['password' => 'wrong-password'])
            ->assertSessionHasErrors(['password' => __('validation.current_password')]);
    }

    $this->actingAs($user)
        ->post(route('password.confirm.store'), ['password' => 'password'])
        ->assertSessionHasErrors(['password' => __('auth.confirm-password.status.throttled', ['seconds' => 60])])
        ->assertSessionMissing('auth.password_confirmed_at');

    $this->travel(60)->seconds();

    $this->actingAs($user)
        ->post(route('password.confirm.store'), ['password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('auth.password_confirmed_at');
});
