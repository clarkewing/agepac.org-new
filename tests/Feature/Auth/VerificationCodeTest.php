<?php

use App\Models\User;
use App\Notifications\VerificationCode;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\OneTimePasswords\Enums\ConsumeOneTimePasswordResult;

beforeEach(function () {
    Notification::fake();
    $this->freezeSecond();

    $this->component = Livewire::test('auth.verification-code', [
        'user' => $this->user = User::factory()->create(),
    ]);
});

it('emails a code when it mounts', function () {
    Notification::assertSentToTimes($this->user, VerificationCode::class, 1);
});

it('does not email another code while one is live', function () {
    // Instantiate a second verification code component to run mount() twice.
    Livewire::test('auth.verification-code', ['user' => $this->user]);

    Notification::assertSentToTimes($this->user, VerificationCode::class, 1);
});

it('records the verified member in the session and announces it', function () {
    $this->component
        ->set('verification_code', $this->user->oneTimePasswords()->value('password'))
        ->call('verify')
        ->assertHasNoErrors()
        ->assertDispatched('code-verified');

    expect(session('email-verified'))
        ->toBe(['user_id' => $this->user->id, 'at' => now()->getTimestamp()]);
});

it('requires a six-digit code', function () {
    $this->component
        ->set('verification_code', '12')
        ->call('verify')
        ->assertHasErrors([
            'verification_code' => 'digits',
        ])
        ->assertNotDispatched('code-verified');
});

it('rejects an incorrect code', function () {
    $incorrectCode = sprintf('%06d',
        ((int) $this->user->oneTimePasswords()->value('password') + 1) % 1_000_000
    );

    $this->component
        ->set('verification_code', $incorrectCode)
        ->call('verify')
        ->assertHasErrors([
            'verification_code' => ConsumeOneTimePasswordResult::IncorrectOneTimePassword->validationMessage(),
        ])
        ->assertNotDispatched('code-verified');

    expect(session('email-verified'))->toBeNull();
});

it('rejects an expired code', function () {
    $this->travel(11)->minutes();

    $this->component
        ->set('verification_code', $this->user->oneTimePasswords()->value('password'))
        ->call('verify')
        ->assertHasErrors([
            'verification_code' => ConsumeOneTimePasswordResult::OneTimePasswordExpired->validationMessage(),
        ])
        ->assertNotDispatched('code-verified');

    expect(session('email-verified'))->toBeNull();
});

it('locks verification after five wrong codes', function () {
    $incorrectCode = sprintf('%06d',
        ((int) $this->user->oneTimePasswords()->value('password') + 1) % 1_000_000
    );

    foreach (range(1, 5) as $attempt) {
        $this->component
            ->set('verification_code', $incorrectCode)
            ->call('verify');
    }

    $this->component
        ->set('verification_code', $this->user->oneTimePasswords()->value('password'))
        ->call('verify')
        ->assertHasErrors([
            'verification_code' => ConsumeOneTimePasswordResult::RateLimitExceeded->validationMessage(),
        ])
        ->assertNotDispatched('code-verified');

    expect(session('email-verified'))->toBeNull();
});

it('refuses a resend within a minute of the last code', function () {
    $this->component
        ->assertSee(Str::before(__('auth.verification-code.resend-wait'), ' :countdown'))
        ->call('resendCode')
        ->assertStatus(429);

    Notification::assertSentToTimes($this->user, VerificationCode::class, 1);
});

it('resends a fresh code once the minute has passed', function () {
    $firstCode = $this->user->oneTimePasswords()->value('password');

    $this->travel(60)->seconds();

    $this->component
        ->call('resendCode')
        ->assertHasNoErrors();

    Notification::assertSentToTimes($this->user, VerificationCode::class, 2);

    $this->component
        ->set('verification_code', $firstCode)
        ->call('verify')
        ->assertHasErrors([
            'verification_code' => ConsumeOneTimePasswordResult::IncorrectOneTimePassword->validationMessage(),
        ]);
});
