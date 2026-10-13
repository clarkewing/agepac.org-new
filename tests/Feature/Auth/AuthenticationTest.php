<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;

it('renders the login screen', function () {
    $this->get(route('login'))->assertOk();
})->skip(message: 'Handoff is handling this route for now');

it('only shows the developer login in the local environment', function () {
    withEnvironment('local', function () {
        loginForm()->assertSeeHtml('<form method="POST" action="'.route('loginLinkLogin').'">');
    });

    withEnvironment('production', function () {
        loginForm()->assertDontSeeHtml('<form method="POST" action="'.route('loginLinkLogin').'">');
    });
});

it('logs the user in with valid credentials', function () {
    $user = User::factory()->create();

    attemptLogin($user->email, 'password')
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

it('rejects an invalid password', function () {
    $user = User::factory()->create();

    attemptLogin($user->email, 'wrong-password')
        ->assertSessionHasErrors(['email' => __('auth.failed')]);

    $this->assertGuest();
});

it('focuses the password field when the email is already filled in', function () {
    loginForm()
        ->assertSeeHtml('autofocus="autofocus" autocomplete="email"')
        ->assertDontSeeHtml('autofocus="autofocus" autocomplete="current-password"');

    loginForm(['_old_input' => ['email' => 'member@example.com']])
        ->assertSeeHtml('value="member@example.com"')
        ->assertDontSeeHtml('autofocus="autofocus" autocomplete="email"')
        ->assertSeeHtml('autofocus="autofocus" autocomplete="current-password"');
});

it('logs the user out to the public site', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('public.home'));

    $this->assertGuest();
});

/**
 * Render the login form through a real request, with the given session data,
 * since the login route itself still redirects to the legacy app.
 *
 * @param  array<string, mixed>  $session
 */
function loginForm(array $session = []): TestResponse
{
    registerLoginForm();

    return test()->withSession($session)->get('login-form');
}

function registerLoginForm(): void
{
    Route::middleware('web')->get('login-form', fn () => view('pages.auth.login'));
}

function attemptLogin(string $email, string $password): TestResponse
{
    return test()->post(route('login.store'), [
        'email' => $email,
        'password' => $password,
    ]);
}
