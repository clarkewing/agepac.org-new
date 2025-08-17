<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    Notification::fake();

    $this->user = User::factory()->create();
});

test('forgot password screen can be rendered', function () {
    $this->get(route('password.request'))
        ->assertOk()
        ->assertSeeLivewire(ForgotPassword::class);
});

test('reset password link can be requested', function () {
    Livewire::test(ForgotPassword::class)
        ->set('email', $this->user->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($this->user, ResetPasswordNotification::class);
});

test('reset password screen can be rendered', function () {
    Livewire::test(ForgotPassword::class)
        ->set('email', $this->user->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($this->user, ResetPasswordNotification::class, function ($notification) {
        $this->get(route('password.reset', ['token' => $notification->token]))
            ->assertOk()
            ->assertSeeLivewire(ResetPassword::class);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Livewire::test(ForgotPassword::class)
        ->set('email', $this->user->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($this->user, ResetPasswordNotification::class, function ($notification) {
        Livewire::test(ResetPassword::class, ['token' => $notification->token])
            ->set('email', $this->user->email)
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});

describe('validation', function () {
    it('rejects an invalid email', function () {
        Livewire::test(ForgotPassword::class)
            ->set('email', $this->user->email)
            ->call('sendPasswordResetLink');

        Notification::assertSentTo($this->user, ResetPasswordNotification::class, function ($notification) {
            Livewire::test(ResetPassword::class, ['token' => $notification->token])
                ->set('email', 'invalid@grounded-labs.co')
                ->set('password', 'password')
                ->set('password_confirmation', 'password')
                ->call('resetPassword')
                ->assertHasErrors(['email' => __('auth.reset-password.status.user')]);

            return true;
        });
    });

    it('rejects an invalid token', function () {
        Livewire::test(ResetPassword::class, ['token' => 'invalid-token'])
            ->set('email', $this->user->email)
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('resetPassword')
            ->assertHasErrors(['email' => __('auth.reset-password.status.token')]);
    });

    it('rejects an expired token', function () {
        Livewire::test(ForgotPassword::class)
            ->set('email', $this->user->email)
            ->call('sendPasswordResetLink');

        Notification::assertSentTo($this->user, ResetPasswordNotification::class, function ($notification) {
            // Travel forward in time beyond token expiration (61 minutes)
            $this->travel(61)->minutes();

            Livewire::test(ResetPassword::class, ['token' => $notification->token])
                ->set('email', $this->user->email)
                ->set('password', 'password')
                ->set('password_confirmation', 'password')
                ->call('resetPassword')
                ->assertHasErrors(['email' => __('auth.reset-password.status.token')]);

            return true;
        });
    });
});
