<?php

use App\Models\User;
use App\Services\Auth\AccountRecovery;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

it('renders the forgot password screen', function () {
    $this->get(route('password.request'))
        ->assertOk();
});

it('emails a reset link', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', __('passwords.sent'));

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

it('answers an unknown email exactly like a known one', function () {
    Notification::fake();

    $this->post(route('password.email'), ['email' => 'nobody@example.com'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', __('passwords.sent'));

    Notification::assertNothingSent();
});

it('sends an imported account to the recovery wizard instead of a reset link', function () {
    Notification::fake();
    $user = User::factory()->migrationRequired()->create();

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertRedirect(route('recovery'))
        ->assertSessionHas(AccountRecovery::SESSION_KEY.'.user_id', $user->id);

    Notification::assertNotSentTo($user, ResetPasswordNotification::class);
});

it('recognises an imported account whatever the case of the email typed', function () {
    $user = User::factory()->migrationRequired()->create();

    $this->post(route('password.email'), ['email' => Str::upper($user->email)])
        ->assertRedirect(route('recovery'))
        ->assertSessionHas(AccountRecovery::SESSION_KEY.'.user_id', $user->id);
});

it('emails a post-incident account an ordinary reset link', function () {
    Notification::fake();
    $user = User::factory()->passwordResetRequired()->create();

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', __('passwords.sent'))
        ->assertSessionMissing(AccountRecovery::SESSION_KEY);

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

it('renders the reset password screen from the emailed link', function () {
    $user = User::factory()->create();

    $this->get(route('password.reset', ['token' => Password::createToken($user), 'email' => $user->email]))
        ->assertOk()
        ->assertSee($user->email);
});

it('resets the password with a valid token', function () {
    $user = User::factory()->create();

    $this->post(route('password.update'), [
        'token' => Password::createToken($user),
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('login'))
        ->assertSessionHas('status', __('passwords.reset'))
        ->assertSessionHasInput('email', $user->email);

    expect(Auth::validate(['email' => $user->email, 'password' => 'new-password']))->toBeTrue();
});

it('rejects a token issued for another email', function () {
    $user = User::factory()->create();

    $this->post(route('password.update'), [
        'token' => Password::createToken($user),
        'email' => 'someone-else@example.com',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertSessionHasErrors(['email' => __('passwords.user')]);

    expect(Auth::validate(['email' => $user->email, 'password' => 'new-password']))->toBeFalse();
});

it('rejects an invalid token', function () {
    $user = User::factory()->create();

    $this->post(route('password.update'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertSessionHasErrors(['email' => __('passwords.token')]);
});

it('rejects an expired token', function () {
    $this->freezeTime();
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $this->travel(61)->minutes();

    $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertSessionHasErrors(['email' => __('passwords.token')]);
});

it('requires a confirmed password', function () {
    $user = User::factory()->create();

    $this->post(route('password.update'), [
        'token' => Password::createToken($user),
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'different-password',
    ])->assertSessionHasErrors(['password' => __('validation.confirmed', ['attribute' => 'password'])]);
});
