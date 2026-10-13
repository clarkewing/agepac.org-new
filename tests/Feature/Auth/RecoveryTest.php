<?php

use App\Actions\StartRecovery;
use App\Enums\ClassCourse;
use App\Models\User;
use App\Notifications\VerificationCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Entry Points
|--------------------------------------------------------------------------
*/

it('sends a login for a sentinel account to the recovery wizard', function (string $state) {
    $user = User::factory()->{$state}()->create();

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'anything'])
        ->assertRedirect(route('recovery'))
        ->assertSessionHas(StartRecovery::SESSION_KEY.'.user_id', $user->id);

    $this->assertGuest();
})->with(['passwordResetRequired', 'migrationRequired']);

it('redirects visitors without a pending recovery to the login page', function () {
    $this->get(route('recovery'))->assertRedirect(route('login'));
});

it('drops a recovery for an account that no longer needs one', function () {
    $user = User::factory()->create();

    startRecoveryFor($user)
        ->get(route('recovery'))
        ->assertRedirect(route('login'))
        ->assertSessionMissing(StartRecovery::SESSION_KEY);
});

/*
|--------------------------------------------------------------------------
| Code Verification
|--------------------------------------------------------------------------
*/

it('emails a one-time code when the wizard opens', function () {
    Notification::fake();
    $user = User::factory()->passwordResetRequired()->create();

    startRecoveryFor($user)
        ->get(route('recovery'))
        ->assertSeeLivewire('pages::auth.recover')
        ->assertSeeHtml('<title>'.__('auth.recovery.title').' · '.config('app.name').'</title>');

    Notification::assertSentToTimes($user, VerificationCode::class, 1);
});

it('does not expose the profile before the code is verified', function () {
    Notification::fake();
    $user = User::factory()->migrationRequired()->create(['phone' => '+33612345678']);

    $wizard = openWizardFor($user);

    $wizard->assertSet('phone', '');

    $wizard->set('code', sentCode($user))->call('verify')
        ->assertHasNoErrors()
        ->assertSet('phone', '+33 6 12 34 56 78');
});

it('rejects a wrong code', function () {
    Notification::fake();
    $user = User::factory()->passwordResetRequired()->create();

    openWizardFor($user)
        ->set('code', '000000')
        ->call('verify')
        ->assertHasErrors(['code' => __('auth.recovery.status.invalid-code')]);
});

it('rejects an expired code', function () {
    Notification::fake();
    $this->freezeTime();
    $user = User::factory()->passwordResetRequired()->create();

    $wizard = openWizardFor($user);

    $this->travel(11)->minutes();

    $wizard->set('code', sentCode($user))
        ->call('verify')
        ->assertHasErrors(['code' => __('auth.recovery.status.invalid-code')]);
});

it('throttles code verification after five failed attempts', function () {
    Notification::fake();
    $this->freezeTime();
    $user = User::factory()->passwordResetRequired()->create();

    $wizard = openWizardFor($user);

    foreach (range(1, 5) as $attempt) {
        $wizard->set('code', '000000')->call('verify')
            ->assertHasErrors(['code' => __('auth.recovery.status.invalid-code')]);
    }

    $wizard->set('code', sentCode($user))->call('verify')
        ->assertHasErrors(['code' => __('auth.recovery.status.too-many-attempts', ['countdown' => '10 minutes'])])
        ->assertSee(Str::before(__('auth.recovery.status.too-many-attempts'), ' :countdown'))
        ->assertSee('until: '.now()->addSeconds(600)->getTimestamp(), escape: false)
        ->assertSee('10 minutes');
});

it('refuses a resend within a minute of the last code', function () {
    Notification::fake();
    $this->freezeTime();
    $user = User::factory()->passwordResetRequired()->create();

    openWizardFor($user)
        ->call('resendCode')
        ->assertStatus(429);

    Notification::assertSentToTimes($user, VerificationCode::class, 1);
});

it('resends a fresh code once the minute has passed', function () {
    Notification::fake();
    $this->freezeTime();
    $user = User::factory()->passwordResetRequired()->create();

    $wizard = openWizardFor($user);
    $firstCode = sentCode($user);

    $this->travel(60)->seconds();

    $wizard->call('resendCode')->assertHasNoErrors();

    Notification::assertSentToTimes($user, VerificationCode::class, 2);

    $wizard->set('code', $firstCode)->call('verify')
        ->assertHasErrors(['code' => __('auth.recovery.status.invalid-code')]);
});

it('throttles resends after three codes', function () {
    Notification::fake();
    $this->freezeTime();
    $user = User::factory()->passwordResetRequired()->create();

    $wizard = openWizardFor($user);

    foreach (range(1, 2) as $resend) {
        $this->travel(60)->seconds();
        $wizard->call('resendCode')->assertHasNoErrors();
    }

    $this->travel(60)->seconds();
    $wizard->call('resendCode')->assertStatus(429);

    Livewire::test('pages::auth.recover')
        ->assertSee(Str::before(__('auth.recovery.status.too-many-codes'), ' :countdown'));

    Notification::assertSentToTimes($user, VerificationCode::class, 3);
});

/*
|--------------------------------------------------------------------------
| Completion
|--------------------------------------------------------------------------
*/

it('forbids completing the wizard before the code is verified', function () {
    Notification::fake();
    $user = User::factory()->passwordResetRequired()->create();

    openWizardFor($user)
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('complete')
        ->assertForbidden();

    expect($user->fresh())
        ->requiresPasswordReset()->toBeTrue();
    $this->assertGuest();
});

it('sets a new password and logs in an account flagged for a password reset', function () {
    Notification::fake();
    $user = User::factory()->passwordResetRequired()->create();

    verifiedWizardFor($user)
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('complete')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $user->refresh();

    expect($user)
        ->requiresRecovery()->toBeFalse();
    expect(Auth::validate(['email' => $user->email, 'password' => 'new-password']))->toBeTrue();
    expect(session()->has(StartRecovery::SESSION_KEY))->toBeFalse();
    $this->assertAuthenticatedAs($user);
});

it('requires a confirmed password', function () {
    Notification::fake();
    $user = User::factory()->passwordResetRequired()->create();

    verifiedWizardFor($user)
        ->set('password', 'new-password')
        ->set('password_confirmation', 'different-password')
        ->call('complete')
        ->assertHasErrors(['password' => 'confirmed']);

    expect($user->fresh())
        ->requiresPasswordReset()->toBeTrue();
});

it('requires the missing profile fields from an imported account', function () {
    Notification::fake();
    $user = User::factory()->migrationRequired()->create([
        'class_course' => null,
        'class_year' => null,
        'birth_date' => null,
        'phone' => null,
    ]);

    verifiedWizardFor($user)
        ->call('complete')
        ->assertHasErrors([
            'password' => 'required',
            'class_course' => 'required',
            'class_year' => 'required',
            'birth_date' => 'required',
            'phone' => 'required',
        ]);
});

it('completes an imported account, verifies its email and logs it in', function () {
    Notification::fake();
    $this->freezeSecond();
    $user = User::factory()->migrationRequired()->unverified()->create([
        'class_course' => null,
        'class_year' => null,
        'birth_date' => null,
        'phone' => null,
    ]);

    verifiedWizardFor($user)
        ->set('class_course', 'EPL/S')
        ->set('class_year', '2015')
        ->set('gender', 'F')
        ->set('birth_date', '1994-09-22')
        ->set('phone', '+33612345678')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('complete')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $user->refresh();

    expect($user)
        ->requiresRecovery()->toBeFalse()
        ->class_course->toBe(ClassCourse::from('EPL/S'))
        ->class_year->toEqual(2015)
        ->gender->toBe('F')
        ->birth_date->toDateString()->toBe('1994-09-22')
        ->phone->formatE164()->toBe('+33612345678')
        ->email_verified_at->toEqual(now());
    expect(Auth::validate(['email' => $user->email, 'password' => 'new-password']))->toBeTrue();
    $this->assertAuthenticatedAs($user);
});

it('does not ask an imported account for a class it already has', function () {
    Notification::fake();
    $user = User::factory()->migrationRequired()->create([
        'class_course' => 'EPL/U',
        'class_year' => 2010,
    ]);

    verifiedWizardFor($user)
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('complete')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user)
        ->class_course->toBe(ClassCourse::from('EPL/U'))
        ->class_year->toEqual(2010);
});

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function startRecoveryFor(User $user): TestCase
{
    return test()->withSession([StartRecovery::SESSION_KEY => ['user_id' => $user->id]]);
}

function openWizardFor(User $user): Testable
{
    startRecoveryFor($user);

    return Livewire::test('pages::auth.recover');
}

function sentCode(User $user): string
{
    $code = null;

    Notification::assertSentTo($user, VerificationCode::class, function (VerificationCode $notification) use (&$code): bool {
        $code = $notification->code;

        return true;
    });

    return $code;
}

function verifiedWizardFor(User $user): Testable
{
    $wizard = openWizardFor($user);

    return $wizard->set('code', sentCode($user))->call('verify')->assertHasNoErrors();
}
