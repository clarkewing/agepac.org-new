<?php

use App\Actions\SendVerificationCode;
use App\Models\User;
use App\Notifications\VerificationCode;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

beforeEach(function () {
    Notification::fake();
    $this->freezeSecond();

    $this->user = User::factory()->create();
    $this->send = resolve(SendVerificationCode::class);
});

it('emails a code that lives ten minutes', function () {
    ($this->send)($this->user);

    Notification::assertSentTo($this->user, VerificationCode::class, function (VerificationCode $notification): bool {
        return $notification->oneTimePassword->expires_at->equalTo(now()->addMinutes(10));
    });
    expect($this->send->isPending($this->user))->toBeTrue();
});

it('refuses sending a second code within a minute', function () {
    ($this->send)($this->user);

    expect(fn () => ($this->send)($this->user))
        ->toThrow(TooManyRequestsHttpException::class);

    expect($this->send->availableAt($this->user))->toBe(now()->addMinute()->getTimestamp());

    Notification::assertSentToTimes($this->user, VerificationCode::class, 1);
});

it('accepts sending another code once the minute has passed', function () {
    ($this->send)($this->user);
    expect($this->send->availableAt($this->user))->toBe(now()->addMinute()->getTimestamp());

    $this->travel(60)->seconds();
    expect($this->send->availableAt($this->user))->toBeNull();

    ($this->send)($this->user);
    Notification::assertSentToTimes($this->user, VerificationCode::class, 2);
});

it('knows when a code has expired', function () {
    ($this->send)($this->user);

    $this->travel(10)->minutes();

    expect($this->send->isPending($this->user))->toBeFalse();
});
