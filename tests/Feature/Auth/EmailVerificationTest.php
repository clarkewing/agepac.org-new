<?php

use App\Livewire\Auth\VerifyEmail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

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
        ->assertSeeLivewire(VerifyEmail::class);
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

it('can resend verification email', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    Livewire::test(VerifyEmail::class)
        ->call('sendVerification')
        ->assertHasNoErrors();

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

it('redirects already verified users when trying to resend verification', function () {
    // Already verified
    $this->actingAs(User::factory()->create());

    Livewire::test(VerifyEmail::class)
        ->call('sendVerification')
        ->assertRedirect(route('dashboard', absolute: false));
});

/*
|--------------------------------------------------------------------------
| Logging Out from Verification Screen
|--------------------------------------------------------------------------
*/

it('can logout from the verification screen', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user);

    Livewire::test(VerifyEmail::class)
        ->call('logout')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

function verificationUrl(User $user): string
{
    return URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );
}
