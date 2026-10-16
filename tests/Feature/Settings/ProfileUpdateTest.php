<?php

use App\Enums\ClassCourse;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    Notification::fake();

    $this->user = User::factory()->create([
        'first_name' => 'Jean',
        'last_name' => 'Mermoz',
        'class_course' => 'EPL/S',
        'class_year' => 2015,
    ]);

    $this->actingAs($this->user);
});

it('renders the profile page', function () {
    $this->get(route('settings.profile'))
        ->assertSuccessful()
        ->assertSeeLivewire('pages::settings.profile');
});

it('shows the member’s name and class as they were approved', function () {
    Livewire::test('pages::settings.profile')
        ->assertSet('first_name', 'Jean')
        ->assertSet('last_name', 'Mermoz')
        ->assertSet('class_course', 'EPL/S')
        ->assertSet('class_year', '2015');
});

it('never saves the name or class', function () {
    Livewire::test('pages::settings.profile')
        ->set('first_name', 'Jacques')
        ->set('class_course', 'EPL/P')
        ->set('class_year', '2020')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($this->user->refresh())
        ->first_name->toBe('Jean')
        ->class_course->toBe(ClassCourse::from('EPL/S'))
        ->class_year->toEqual(2015);
});

it('updates the member’s details and email address', function () {
    Livewire::test('pages::settings.profile')
        ->set('gender', 'F')
        ->set('birth_date', '1994-09-22')
        ->set('phone', '+33612345678')
        ->set('email', 'new@example.com')
        ->call('updateProfileInformation')
        ->assertHasNoErrors()
        ->assertDispatched('toast-show',
            fn (string $name, array $params): bool => $params['slots']['text'] === __('settings.profile.status.saved')
        );

    expect($this->user->refresh())
        ->gender->toBe('F')
        ->birth_date->toDateString()->toBe('1994-09-22')
        ->phone->formatE164()->toBe('+33612345678')
        ->email->toBe('new@example.com')
        ->email_verified_at->toBeNull();
    Notification::assertSentTo($this->user, VerifyEmailNotification::class);
});

it('keeps the email verified when the address is unchanged', function () {
    Livewire::test('pages::settings.profile')
        ->set('gender', 'M')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($this->user->refresh()->email_verified_at)->not->toBeNull();
    Notification::assertNothingSent();
});

it('requires valid details', function () {
    Livewire::test('pages::settings.profile')
        ->set('gender', '')
        ->set('birth_date', 'not-a-date')
        ->set('phone', '123')
        ->call('updateProfileInformation')
        ->assertHasErrors(['gender' => 'required', 'birth_date', 'phone']);
});

describe('account deletion', function () {
    it('deletes the member’s account', function () {
        Livewire::test('pages::settings.delete-user-form')
            ->set('password', 'password')
            ->call('deleteUser')
            ->assertHasNoErrors()
            ->assertSessionHas('status', __('settings.profile.delete-account.status.deleted'))
            ->assertRedirect(route('login'));

        expect($this->user->fresh())->toBeNull();
        $this->assertGuest();
    });

    it('requires the correct password to delete the account', function () {
        Livewire::test('pages::settings.delete-user-form')
            ->set('password', 'wrong-password')
            ->call('deleteUser')
            ->assertHasErrors(['password']);

        expect($this->user->fresh())->not->toBeNull();
    });
});

describe('resending the verification email', function () {
    it('can resend verification email', function () {
        $this->user->forceFill(['email_verified_at' => null])->save();

        Livewire::test('pages::settings.profile')
            ->call('resendVerificationNotification')
            ->assertHasNoErrors()
            ->assertDispatched('toast-show', fn (string $name, array $params): bool => $params['slots']['text'] === __('settings.profile.email-verification.status.verification-link-sent'));

        Notification::assertSentTo($this->user, VerifyEmailNotification::class);
    });

    it('does nothing once the address is verified', function () {
        Livewire::test('pages::settings.profile')
            ->call('resendVerificationNotification')
            ->assertNoRedirect()
            ->assertNotDispatched('toast-show');

        Notification::assertNothingSent();
    });
});
