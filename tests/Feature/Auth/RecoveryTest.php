<?php

use App\Enums\ClassCourse;
use App\Http\Middleware\RedirectIfRecoveryRequired;
use App\Models\User;
use App\Notifications\VerificationCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

describe('entry points', function () {
    it('sends a login for a sentinel account to the recovery page', function (string $state) {
        $user = User::factory()->{$state}()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'anything',
        ])
            ->assertRedirect(route('recovery'))
            ->assertSessionHas(RedirectIfRecoveryRequired::SESSION_KEY.'.user_id', $user->id);

        $this->assertGuest();
    })->with(['passwordResetRequired', 'migrationRequired']);

    it('redirects visitors without a pending recovery to the login page', function () {
        $this->get(route('recovery'))
            ->assertRedirect(route('login'));
    });

    it('exits recovery for an account that no longer needs one', function () {
        $this->withSession([RedirectIfRecoveryRequired::SESSION_KEY => ['user_id' => User::factory()->create()->id]]);

        $this->get(route('recovery'))
            ->assertRedirect(route('login'))
            ->assertSessionMissing(RedirectIfRecoveryRequired::SESSION_KEY);
    });
});

describe('verification gate', function () {
    beforeEach(function () {
        Notification::fake();
        $this->user = User::factory()->passwordResetRequired()->create();

        $this->withSession([RedirectIfRecoveryRequired::SESSION_KEY => ['user_id' => $this->user->id]]);
    });

    it('asks for a one-time code first and emails it', function () {
        $this->get(route('recovery'))
            ->assertSeeLivewire('pages::auth.recover')
            ->assertSeeLivewire('auth.verification-code')
            ->assertSee(__('auth.recovery.callouts.incident.heading'))
            ->assertSeeHtml('<title>'.__('auth.recovery.title').' · '.config('app.name').'</title>');

        Notification::assertSentToTimes($this->user, VerificationCode::class, 1);
    });

    it('moves on once the session records the verification', function () {
        $page = Livewire::test('pages::auth.recover');

        Livewire::test('auth.verification-code', ['user' => $this->user])
            ->set('verification_code', $this->user->oneTimePasswords()->value('password'))
            ->call('verify');

        $page->dispatch('code-verified')
            ->assertSee(__('auth.recovery.steps.password.description'));
    });

    it('stays on the verification step when the session does not back the event', function () {
        Livewire::test('pages::auth.recover')
            ->dispatch('code-verified')
            ->assertSee(__('auth.recovery.steps.verify.description'))
            ->assertDontSee(__('auth.recovery.steps.password.description'));
    });

    it('resets the password of a post-incident account from the login form in the browser', function () {
        config()->set('app.locale', 'fr');
        app()->setLocale('fr');
        pest()->browser()->withHost(Route::getRoutes()->getByName('recovery')->getDomain());

        visit(route('login', absolute: false))
            ->fill('email', $this->user->email)
            ->fill('password', 'anything')
            ->click(__('auth.login.action'))
            ->assertSee(__('auth.recovery.callouts.incident.heading'))
            ->fill('[data-flux-otp-input] >> nth=0', $this->user->oneTimePasswords()->value('password'))
            ->assertSee(__('auth.recovery.steps.password.description'))
            ->fill('password', 'top-secret-password')
            ->fill('password_confirmation', 'top-secret-password')
            ->assertNoJavaScriptErrors()
            ->click(__('auth.recovery.action'))
            ->assertPathIsNot(route('recovery', absolute: false));

        expect($this->user->fresh())->requiresRecovery()->toBeFalse();
        expect(Auth::validate(['email' => $this->user->email, 'password' => 'top-secret-password']))->toBeTrue();
    })->skip(message: 'Pending PR 4: GET /login still redirects to the legacy app');
});

describe('password reset', function () {
    beforeEach(function () {
        Notification::fake();
        $this->user = User::factory()->passwordResetRequired()->create();

        $this->withSession([
            RedirectIfRecoveryRequired::SESSION_KEY => ['user_id' => $this->user->id],
            'email-verified' => ['user_id' => $this->user->id, 'at' => now()->getTimestamp()],
        ]);
    });

    it('asks an account flagged for a reset for a new password', function () {
        Livewire::test('pages::auth.recover')
            ->assertSee(__('auth.recovery.heading.password-reset'))
            ->assertSee(__('auth.recovery.steps.password.description'))
            ->assertSee(__('auth.recovery.action'))
            ->assertDontSee(__('auth.recovery.callouts.incident.heading'))
            ->assertDontSeeLivewire('auth.verification-code');
    });

    it('sets the new password and logs the member in', function () {
        Livewire::test('pages::auth.recover')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        expect($this->user->fresh())->requiresRecovery()->toBeFalse();
        expect(Auth::validate(['email' => $this->user->email, 'password' => 'new-password']))->toBeTrue();
        expect(session()->has(RedirectIfRecoveryRequired::SESSION_KEY))->toBeFalse();
        $this->assertAuthenticatedAs($this->user);
    });

    it('verifies the email of an account that had not verified it', function () {
        $this->freezeSecond();
        $this->user->forceFill(['email_verified_at' => null])->save();

        Livewire::test('pages::auth.recover')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('resetPassword');

        expect($this->user->fresh())->email_verified_at->toEqual(now());
    });

    it('keeps the date an account first verified its email', function () {
        $this->user->forceFill([
            'email_verified_at' => $verifiedAt = now()->subYear()->startOfSecond(),
        ])->save();

        Livewire::test('pages::auth.recover')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('resetPassword');

        expect($this->user->fresh())->email_verified_at->toEqual($verifiedAt);
    });

    it('requires a confirmed password', function () {
        Livewire::test('pages::auth.recover')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'different-password')
            ->call('resetPassword')
            ->assertHasErrors(['password' => 'confirmed']);

        expect($this->user->fresh())->requiresPasswordReset()->toBeTrue();
        $this->assertGuest();
    });

    it('forbids setting a password before the code is verified', function () {
        session()->forget('email-verified');

        Livewire::test('pages::auth.recover')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('resetPassword')
            ->assertForbidden();

        expect($this->user->fresh())->requiresPasswordReset()->toBeTrue();
        $this->assertGuest();
    });
});

describe('migration', function () {
    beforeEach(function () {
        Notification::fake();
        $this->user = User::factory()->migrationRequired()->create();

        $this->withSession([RedirectIfRecoveryRequired::SESSION_KEY => ['user_id' => $this->user->id]]);
    });

    it('keeps an imported account on the verification step until it is verified', function () {
        Livewire::test('pages::auth.recover')
            ->assertSeeLivewire('auth.verification-code')
            ->assertNoRedirect()
            ->assertDontSee(__('auth.recovery.callouts.incident.heading'));
    });

    it('sends an imported account on to the migration once verified', function () {
        $page = Livewire::test('pages::auth.recover');

        Livewire::test('auth.verification-code', ['user' => $this->user])
            ->set('verification_code', $this->user->oneTimePasswords()->value('password'))
            ->call('verify');

        $page->dispatch('code-verified')
            ->assertRedirect(route('recovery.migrate'));
    });

    it('sends an already verified imported account straight to the migration', function () {
        $this->withSession(['email-verified' => ['user_id' => $this->user->id, 'at' => now()->getTimestamp()]]);

        Livewire::test('pages::auth.recover')
            ->assertRedirect(route('recovery.migrate'));
    });

    it('migrates an imported account from the login form in the browser', function () {
        config()->set('app.locale', 'fr');
        app()->setLocale('fr');
        pest()->browser()->withHost(Route::getRoutes()->getByName('recovery')->getDomain());

        $this->user->forceFill([
            'class_course' => null,
            'class_year' => null,
            'gender' => 'F',
            'birth_date' => '1994-09-22',
            'phone' => '+33612345678',
        ])->save();

        visit(route('login', absolute: false))
            ->fill('email', $this->user->email)
            ->fill('password', 'anything')
            ->click(__('auth.login.action'))
            ->assertSee(__('auth.verification-code.code-label'))
            ->fill('[data-flux-otp-input] >> nth=0', $this->user->oneTimePasswords()->value('password'))
            ->assertPathIs(route('recovery.migrate', absolute: false))
            ->assertValue('[wire\\:model\\.self=phone] input[type="tel"]', '06 12 34 56 78')
            ->select('class_course', 'EPL/S')
            ->fill('class_year', '2015')
            ->click(__('wizard.continue'))
            ->assertSee(__('auth.recovery.steps.password.description'))
            ->fill('password', 'top-secret-password')
            ->fill('password_confirmation', 'top-secret-password')
            ->assertNoJavaScriptErrors()
            ->click(__('auth.recovery.action'))
            ->assertPathIsNot(route('recovery.migrate', absolute: false));

        expect($this->user->fresh())
            ->requiresRecovery()->toBeFalse()
            ->class_course->toBe(ClassCourse::from('EPL/S'))
            ->class_year->toEqual(2015)
            ->phone->formatE164()->toBe('+33612345678');
    })->skip(message: 'Pending PR 4: GET /login still redirects to the legacy app');
});
