<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

beforeEach(function () {
    Event::fake();
});

/*
|--------------------------------------------------------------------------
| Rendering
|--------------------------------------------------------------------------
*/

it('renders the email verification screen', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertOk()
        ->assertSee($user->email)
        ->assertSeeHtml(route('settings.profile'))
        ->assertSeeHtml(route('logout'));
});

it('sends unverified users to the verification notice from verified-only pages', function () {
    Route::middleware(['web', 'auth', 'verified'])->get('verified-only', fn () => 'ok');

    $this->actingAs(User::factory()->unverified()->create())
        ->get('verified-only')
        ->assertRedirect(route('verification.notice'))
        ->assertDontSee('ok');

    $this->actingAs(User::factory()->create())
        ->get('verified-only')
        ->assertOk()
        ->assertContent('ok');
});

it('sends an already verified user past the verification screen', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('verification.notice'))
        ->assertRedirect(route('dashboard', absolute: false));
});

it('requires the user to be authenticated to proceed', function () {
    $user = User::factory()->unverified()->create();

    $this->get(verificationUrl($user))
        ->assertRedirect(route('login', absolute: false));
});

/*
|--------------------------------------------------------------------------
| Successful Verification
|--------------------------------------------------------------------------
*/

it('verifies the email using a signed URL', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(verificationUrl($user))
        ->assertRedirect(route('dashboard', absolute: false).'?verified=1');

    Event::assertDispatched(Verified::class);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Failed / Invalid Verification
|--------------------------------------------------------------------------
*/

it('does not verify the email if the hash is invalid', function () {
    $user = User::factory()->unverified()->create();

    $invalidUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)
        ->get($invalidUrl)
        ->assertForbidden();

    Event::assertNotDispatched(Verified::class);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Already Verified
|--------------------------------------------------------------------------
*/

it('redirects when the email is already verified', function () {
    $user = User::factory()->create();

    expect($user->hasVerifiedEmail())->toBeTrue();

    $this->actingAs($user)
        ->get(verificationUrl($user))
        ->assertRedirect(route('dashboard', absolute: false).'?verified=1');

    Event::assertNotDispatched(Verified::class);
});

/*
|--------------------------------------------------------------------------
| Resending Verification Email
|--------------------------------------------------------------------------
*/

it('resends the verification email', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post(route('verification.send'))
        ->assertSessionHas('status', 'verification-link-sent');

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertSee(__('auth.verify-email.status.verification-link-sent', ['email' => $user->email]));

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

it('allows one resend a minute', function () {
    Notification::fake();
    $this->freezeTime();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->post(route('verification.send'))->assertRedirect();
    $this->actingAs($user)->post(route('verification.send'))->assertTooManyRequests();

    Notification::assertSentToTimes($user, VerifyEmailNotification::class, 1);

    $this->travel(60)->seconds();

    $this->actingAs($user)->post(route('verification.send'))->assertRedirect();
});

it('counts down to the next resend instead of offering the button', function () {
    Notification::fake();
    $this->freezeTime();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->from(route('verification.notice'))
        ->followingRedirects()
        ->post(route('verification.send'))
        ->assertSee(Str::before(__('auth.verify-email.resend-wait'), ' :countdown'))
        ->assertSeeHtml('until: '.now()->addSeconds(60)->getTimestamp());
});

it('does not resend to an already verified user', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect(route('dashboard', absolute: false));

    Notification::assertNothingSent();
});

function verificationUrl(User $user): string
{
    return URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );
}
