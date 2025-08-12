<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Livewire\Livewire;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
})->skip(message: 'Handoff is handling this route for now');

test('login screen only shows developer login in local environment', function () {
    withEnvironment('local',
        fn () => Livewire::test('auth.login')
            ->assertSeeHtml('<form method="POST" action="'.route('loginLinkLogin').'">')
    );

    withEnvironment('production',
        fn () => Livewire::test('auth.login')
            ->assertDontSeeHtml('<form method="POST" action="'.route('loginLinkLogin').'">')
    );
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('login');

    $response->assertHasErrors('email');

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('public.home'));

    $this->assertGuest();
});
