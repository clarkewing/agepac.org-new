<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Features\SupportTesting\Testable;
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

    attemptLogin($user->email, 'password')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    attemptLogin($user->email, 'wrong-password')
        ->assertHasErrors('email');

    $this->assertGuest();
});

test('users are rate limited after too many failed login attempts', function () {
    Event::fake();
    $this->freezeTime();

    $user = User::factory()->create();

    // Make 5 failed login attempts (the rate limit threshold)
    for ($i = 0; $i < 5; $i++) {
        attemptLogin($user->email, 'wrong-password')
            ->assertHasErrors('email');
    }

    // The 6th attempt should trigger rate limiting
    $secondsRemaining = RateLimiter::availableIn(throttleKey($user->email));

    attemptLogin($user->email, 'wrong-password')
        ->assertHasErrors([
            'email' => __('auth.login.status.throttle', ['seconds' => $secondsRemaining]),
        ]);

    Event::assertDispatched(Lockout::class);

    // Ensure the rate limit is reset after the time has passed
    $this->travel($secondsRemaining)->seconds();

    attemptLogin($user->email, 'password')
        ->assertHasNoErrors();

    $this->assertAuthenticated();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('public.home'));

    $this->assertGuest();
});

function attemptLogin(string $email, string $password): Testable
{
    return Livewire::test(Login::class)
        ->set('email', $email)
        ->set('password', $password)
        ->call('login');
}

function throttleKey(string $email): string
{
    return Str::transliterate(Str::lower($email).'|'.request()->ip());
}
