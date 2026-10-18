<?php

use App\Models\User;

it('sends a guest at the root on to log in', function () {
    $this->followingRedirects()
        ->get('/')
        ->assertOk()
        ->assertViewIs('pages.auth.login');
});

it('sends a logged-in member at the root straight to the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertRedirect(route('dashboard'));
});
