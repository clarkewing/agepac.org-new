<?php

use App\Http\Responses\Fortify\LockoutResponse;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Event;
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

it('locks the user out for a minute after five failed attempts', function () {
    $user = User::factory()->create();

    Event::fake([Lockout::class]);
    $this->freezeTime();

    foreach (range(1, 5) as $attempt) {
        attemptLogin($user->email, 'wrong-password')
            ->assertSessionHasErrors(['email' => __('auth.failed')]);
    }

    attemptLogin($user->email, 'password')
        ->assertSessionHasErrors(['throttle' => __('auth.throttle', ['seconds' => 60])])
        ->assertSessionHas(LockoutResponse::LOCKOUT_UNTIL_KEY, now()->addSeconds(60)->getTimestamp())
        ->assertSessionHasInput('email', $user->email);

    Event::assertDispatched(Lockout::class);
    $this->assertGuest();

    $this->travel(60)->seconds();

    attemptLogin($user->email, 'password')
        ->assertSessionHasNoErrors();

    $this->assertAuthenticatedAs($user);
});

it('locks the user out for longer with each further failure', function () {
    $user = User::factory()->create();
    $this->freezeTime();

    foreach (range(1, 5) as $attempt) {
        attemptLogin($user->email, 'wrong-password');
    }

    // Each failure once a lockout has ended starts a longer one.
    foreach ([60 => 300, 300 => 900, 900 => 3600, 3600 => 3600] as $currentLockout => $nextLockout) {
        $this->travel($currentLockout)->seconds();

        attemptLogin($user->email, 'wrong-password')
            ->assertSessionHasErrors(['email' => __('auth.failed')]);

        attemptLogin($user->email, 'password')
            ->assertSessionHasErrors(['throttle' => __('auth.throttle', ['seconds' => $nextLockout])]);
    }
});

it('forgets the run of failures after a successful login', function () {
    $user = User::factory()->create();
    $this->freezeTime();

    foreach (range(1, 4) as $attempt) {
        attemptLogin($user->email, 'wrong-password');
    }

    attemptLogin($user->email, 'password')
        ->assertSessionHasNoErrors();
    Auth::logout();

    foreach (range(1, 4) as $attempt) {
        attemptLogin($user->email, 'wrong-password')
            ->assertSessionDoesntHaveErrors('throttle');
    }
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

it('shows the lockout counting down above the login form', function () {
    $this->freezeTime();
    $user = User::factory()->create();
    registerLoginForm();

    foreach (range(1, 5) as $attempt) {
        attemptLogin($user->email, 'wrong-password');
    }

    $this->from('login-form')
        ->followingRedirects()
        ->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
        ->assertSee(__('auth.login.callouts.lockout.heading'))
        ->assertSeeHtml('until: '.now()->addSeconds(60)->getTimestamp())
        ->assertSee('1 minute')
        ->assertSee(__('auth.login.callouts.lockout.done'));
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
