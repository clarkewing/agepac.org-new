<?php

use App\Models\User;
use Illuminate\Support\Uri;

test('admin panel is on squawk subdomain', function () {
    $uri = Uri::route('filament.admin.pages.dashboard');

    expect($uri->host())->toStartWith('squawk.');
});

test('guests are redirected to the login page', function () {
    $this->get(route('filament.admin.pages.dashboard'))
        ->assertRedirect(route('login'));
});

test('users without an admin role cannot access the admin panel', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('filament.admin.pages.dashboard'))
        ->assertForbidden();
});

test('admins can access the admin panel', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('filament.admin.pages.dashboard'))
        ->assertSuccessful();
});

test('the panel follows the session locale', function () {
    $this->actingAs(User::factory()->admin()->create());

    // Default locale: EN
    $this->get(route('filament.admin.pages.dashboard'))
        ->assertSuccessful()
        ->assertSee('Welcome')
        ->assertDontSee('Bonjour');

    $this->withSession(['locale' => 'fr'])
        ->get(route('filament.admin.pages.dashboard'))
        ->assertSuccessful()
        ->assertSee('Bonjour')
        ->assertDontSeeText('Welcome');
});

test('the sidebar links to the admin panel for admins', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('settings.appearance'))
        ->assertSee(route('filament.admin.pages.dashboard'));
});

test('the sidebar does not link to the admin panel for other users', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('settings.appearance'))
        ->assertDontSee(route('filament.admin.pages.dashboard'));
});
