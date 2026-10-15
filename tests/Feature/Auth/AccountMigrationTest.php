<?php

use App\Enums\ClassCourse;
use App\Http\Middleware\RedirectIfRecoveryRequired;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->migrationRequired()->unverified()->create([
        'class_course' => null,
        'class_year' => null,
        'birth_date' => null,
        'phone' => null,
    ]);

    $this->withSession([
        RedirectIfRecoveryRequired::SESSION_KEY => ['user_id' => $this->user->id],
        'email-verified' => ['user_id' => $this->user->id, 'at' => now()->getTimestamp()],
    ]);
});

describe('access', function () {
    it('renders once the member has verified their email', function () {
        $this->get(route('recovery.migrate'))
            ->assertSeeLivewire('pages::auth.migrate')
            ->assertSeeHtml('<title>'.__('auth.recovery.title').' · '.config('app.name').'</title>');
    });

    it('sends the member back to the code when not verified', function () {
        session()->forget('email-verified');

        $this->get(route('recovery.migrate'))
            ->assertRedirect(route('recovery'));
    });

    it('sends an account that needs no migration back to recovery', function () {
        $user = User::factory()->passwordResetRequired()->create();

        $this->withSession([
            RedirectIfRecoveryRequired::SESSION_KEY => ['user_id' => $user->id],
            'email-verified' => ['user_id' => $user->id, 'at' => now()->getTimestamp()],
        ]);

        $this->get(route('recovery.migrate'))
            ->assertRedirect(route('recovery'));
    });
});

describe('wizard', function () {
    it('asks an imported account for its details, then a password', function () {
        Livewire::test('pages::auth.migrate')
            ->assertSet('currentStepName', 'auth.steps.details')
            ->assertSee(__('auth.recovery.heading.migration'))
            ->assertSee(__('auth.recovery.steps.details.description', ['name' => $this->user->first_name]))
            ->dispatch('nextStep', currentStepState: [])
            ->assertSet('currentStepName', 'auth.steps.password')
            ->assertSee(__('auth.recovery.action'))
            ->assertDontSee(__('wizard.continue'));
    });

    it('prefills the details from the import', function () {
        $this->user->forceFill(['phone' => '+33612345678'])->save();

        Livewire::test('pages::auth.migrate')
            ->assertSet('allStepState.auth.steps.details.phone', '+33612345678');
    });

    it('does not ask an imported account for a class it already has', function () {
        $this->user->forceFill(['class_course' => 'EPL/U', 'class_year' => 2010])->save();

        Livewire::test('pages::auth.migrate')
            ->assertSet('allStepState.auth.steps.details.requiresClass', false)
            ->assertDontSee(__('fields.class-course.label'))
            ->dispatch('nextStep', currentStepState: [
                'gender' => 'M',
                'birth_date' => '1994-09-22',
                'phone' => '+33612345678',
            ])
            ->dispatch('nextStep', currentStepState: [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertHasNoErrors();

        $this->assertAuthenticatedAs($this->user);

        expect($this->user->fresh())
            ->class_course->toBe(ClassCourse::from('EPL/U'))
            ->class_year->toEqual(2010);
    });

    it('requires the missing profile fields from an imported account', function () {
        Livewire::test('pages::auth.migrate')
            ->dispatch('nextStep', currentStepState: [])
            ->dispatch('nextStep', currentStepState: [])
            ->assertHasErrors([
                'password' => 'required',
                'class_course' => 'required',
                'class_year' => 'required',
                'birth_date' => 'required',
                'phone' => 'required',
            ]);

        expect($this->user->fresh())->requiresMigration()->toBeTrue();
    });

    it('completes an imported account, verifies its email and logs it in', function () {
        $this->freezeSecond();

        Livewire::test('pages::auth.migrate')
            ->dispatch('nextStep', currentStepState: [
                'class_course' => 'EPL/S',
                'class_year' => '2015',
                'gender' => 'F',
                'birth_date' => '1994-09-22',
                'phone' => '+33612345678',
            ])
            ->dispatch('nextStep', currentStepState: [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        expect($this->user->fresh())
            ->requiresRecovery()->toBeFalse()
            ->class_course->toBe(ClassCourse::from('EPL/S'))
            ->class_year->toEqual(2015)
            ->gender->toBe('F')
            ->birth_date->toDateString()->toBe('1994-09-22')
            ->phone->formatE164()->toBe('+33612345678')
            ->email_verified_at->toEqual(now());
        expect(Auth::validate(['email' => $this->user->email, 'password' => 'new-password']))->toBeTrue();
        expect(session()->has(RedirectIfRecoveryRequired::SESSION_KEY))->toBeFalse();
        $this->assertAuthenticatedAs($this->user);
    });

    it('keeps the date an imported account first verified its email', function () {
        $this->user->forceFill(['email_verified_at' => $verifiedAt = now()->subYear()->startOfSecond()])->save();

        Livewire::test('pages::auth.migrate')
            ->dispatch('nextStep', currentStepState: [
                'class_course' => 'EPL/S',
                'class_year' => '2015',
                'gender' => 'F',
                'birth_date' => '1994-09-22',
                'phone' => '+33612345678',
            ])
            ->dispatch('nextStep', currentStepState: [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertHasNoErrors();

        expect($this->user->fresh())->email_verified_at->toEqual($verifiedAt);
    });
});
