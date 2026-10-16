<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('members pending approval are sent to the page explaining why', function () {
    $this->actingAs(User::factory()->pendingApproval()->create());

    $this->get(route('dashboard'))
        ->assertRedirect(route('pending-approval'));
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get(route('dashboard'))
        ->assertStatus(200)
        ->assertSeeText($user->name);
})->skip(message: 'Handoff is handling this route for now');
