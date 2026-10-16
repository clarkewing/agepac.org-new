<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Enum as EnumRule;
use Livewire\Livewire;
use Propaganistas\LaravelPhone\Rules\Phone;

beforeEach(function () {
    $this->wizard = Livewire::test('pages::auth.register');

    $this->answers = [
        'auth.steps.identity' => ['first_name' => 'Test', 'last_name' => 'User', 'email' => 'test@example.com'],
        'auth.steps.details' => ['class_course' => 'EPL/S', 'class_year' => '2015', 'gender' => 'M', 'birth_date' => '1994-09-22', 'phone' => '+33612345678'],
        'auth.steps.password' => ['password' => 'top-secret-password', 'password_confirmation' => 'top-secret-password'],
    ];
});

describe('wizard', function () {
    it('renders', function () {
        $this->get(route('register'))
            ->assertSuccessful()
            ->assertSeeLivewire('pages::auth.register');
    });

    it('walks through steps in order', function () {
        $steps = [
            'identity',
            'details',
            'password',
            'review',
        ];

        foreach ($steps as $i => $step) {
            if ($i > 0) {
                $this->wizard->dispatch('nextStep', currentStepState: []);
            }

            $this->wizard
                ->assertSet('currentStepName', "auth.steps.$step")
                ->assertSee(__("auth.register.steps.$step.heading"));
        }

        foreach (array_reverse($steps) as $i => $step) {
            if ($i > 0) {
                $this->wizard->dispatch('previousStep', currentStepState: []);
            }

            $this->wizard
                ->assertSet('currentStepName', "auth.steps.$step")
                ->assertSee(__("auth.register.steps.$step.heading"));
        }
    });

    it('returns to the review step after an edit', function () {
        Livewire::test('pages::auth.register', ['showStep' => 'auth.steps.review', 'initialState' => $this->answers])
            ->dispatch('editField', field: 'gender', currentStepState: [])
            ->assertSet('currentStepName', 'auth.steps.details')
            ->assertSet('returnTo', 'auth.steps.review')
            ->dispatch('nextStep', currentStepState: [])
            ->assertSet('returnTo', null)
            ->assertSet('currentStepName', 'auth.steps.review');
    });

    it('forgets a pending return to review when stepping back', function () {
        Livewire::test('pages::auth.register', ['showStep' => 'auth.steps.review', 'initialState' => $this->answers])
            ->dispatch('editField', field: 'password', currentStepState: [])
            ->assertSet('currentStepName', 'auth.steps.password')
            ->assertSet('returnTo', 'auth.steps.review')
            ->dispatch('previousStep', currentStepState: [])
            ->assertSet('returnTo', null)
            ->assertSet('currentStepName', 'auth.steps.details')
            ->dispatch('nextStep', currentStepState: [])
            ->assertSet('returnTo', null)
            ->assertSet('currentStepName', 'auth.steps.password'); // versus details otherwise
    });

    it('creates the account and logs the member in once the review is submitted', function () {
        Event::fake([Registered::class]);

        Livewire::test('pages::auth.register', ['showStep' => 'auth.steps.review', 'initialState' => $this->answers])
            ->dispatch('nextStep', currentStepState: [])
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        expect($user = User::query()->sole())
            ->first_name->toBe('Test')
            ->last_name->toBe('User')
            ->email->toBe('test@example.com')
            ->class_year->toEqual(2015)
            ->gender->toBe('M')
            ->birth_date->toDateString()->toBe('1994-09-22')
            ->phone->formatE164()->toBe('+33612345678');

        $this->assertAuthenticatedAs($user);

        Event::assertDispatched(Registered::class);
    });

    it('refuses an email taken since the identity step, offering to fix it', function () {
        User::factory()->create(['email' => 'test@example.com']);

        Livewire::test('pages::auth.register', ['showStep' => 'auth.steps.review', 'initialState' => $this->answers])
            ->dispatch('nextStep', currentStepState: [])
            ->assertHasErrors(['email' => 'unique'])
            ->assertSee(__('validation.unique', ['attribute' => __('validation.attributes.email')]))
            ->assertSeeHtml("wire:click=\"editField('email')\"")
            ->call('editField', 'email')
            ->assertHasNoErrors()
            ->assertSet('currentStepName', 'auth.steps.identity');

        expect(User::query()->count())->toBe(1);
        $this->assertGuest();
    });

    it('creates an account through the wizard in the browser', function () {
        config()->set('app.locale', 'fr');
        app()->setLocale('fr');
        pest()->browser()->withHost(Route::getRoutes()->getByName('register')->getDomain());

        visit(route('register', absolute: false))
            ->fill('first_name', 'Test')
            ->fill('last_name', 'User')
            ->fill('email', 'test@example.com')
            ->click(__('wizard.continue'))
            ->assertSee(__('auth.register.steps.details.heading'))
            ->select('class_course', 'EPL/S')
            ->fill('class_year', '2015')
            ->select('gender', 'M')
            ->fill('[wire\\:model\\.self=birth_date] input', '1994-09-22')
            ->fill('[wire\\:model\\.self=phone] input[type="tel"]', '0612345678')
            ->click(__('wizard.continue'))
            ->assertSee(__('auth.register.steps.password.heading'))
            ->fill('password', 'top-secret-password')
            ->fill('password_confirmation', 'top-secret-password')
            ->click(__('wizard.continue'))
            ->assertSee(__('auth.register.steps.review.heading'))
            ->assertSee('Test User')
            ->assertSee('+33 6 12 34 56 78')
            ->click(__('auth.register.action'))
            ->assertPathIs(route('verification.notice', absolute: false))
            ->assertNoJavaScriptErrors();

        expect(User::query()->sole())
            ->first_name->toBe('Test')
            ->email->toBe('test@example.com')
            ->class_year->toEqual(2015)
            ->birth_date->toDateString()->toBe('1994-09-22')
            ->phone->formatE164()->toBe('+33612345678');
    });

    it('does not expose a registration endpoint', function () {
        expect(Route::has('register.store'))->toBeFalse();
    });
});

describe('identity step', function () {
    beforeEach(function () {
        $this->step = $this->wizard->testStep('auth.steps.identity');
    });

    it('advances once the step is completed', function () {
        $this->step
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'test@example.com')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatchedTo($this->wizard->instance()->getName(), 'nextStep');
    });

    it('lowercases the email before checking it', function () {
        $this->step
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', ' Test@Example.com ')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('email', 'test@example.com');
    });

    it('rejects invalid input', function (string $field, mixed $value, string $rule) {
        $this->step
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'test@example.com')
            ->set($field, $value)
            ->call('save')
            ->assertHasErrors([$field => $rule])
            ->assertNotDispatched('nextStep');
    })->with([
        'missing first name' => ['first_name', '', 'required'],
        'missing last name' => ['last_name', '', 'required'],
        'missing email' => ['email', '', 'required'],
        'malformed email' => ['email', 'invalid-email', 'email'],
    ]);

    it('rejects an email that already belongs to a member', function () {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $this->step
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'duplicate@example.com')
            ->call('save')
            ->assertHasErrors(['email' => 'unique']);
    });
});

describe('details step', function () {
    beforeEach(function () {
        $this->step = $this->wizard->testStep('auth.steps.details');
    });

    it('advances once the step is completed', function () {
        $this->step
            ->set('class_course', 'EPL/S')
            ->set('class_year', '2015')
            ->set('gender', 'M')
            ->set('birth_date', '1994-09-22')
            ->set('phone', '+33612345678')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatchedTo($this->wizard->instance()->getName(), 'nextStep');
    });

    it('rejects invalid details', function (string $field, mixed $value, string $rule) {
        $this->travelTo(Carbon::create(2026));

        $this->step
            ->set('class_course', 'EPL/S')
            ->set('class_year', '2015')
            ->set('gender', 'M')
            ->set('birth_date', '1994-09-22')
            ->set('phone', '+33612345678')
            ->set($field, $value)
            ->call('save')
            ->assertHasErrors([$field => $rule])
            ->assertNotDispatched('nextStep');
    })->with([
        'missing class course' => ['class_course', '', 'required'],
        'unknown class course' => ['class_course', 'Invalid-Course', EnumRule::class],
        'missing class year' => ['class_year', '', 'required'],
        'malformed class year' => ['class_year', 'not-a-year', 'date_format'],
        'missing gender' => ['gender', '', 'required'],
        'unknown gender' => ['gender', 'X', EnumRule::class],
        'missing birth date' => ['birth_date', '', 'required'],
        'malformed birth date' => ['birth_date', 'not-a-date', 'date_format'],
        'birth date too far back' => ['birth_date', '1800-01-01', 'after'],
        'birth date too recent' => ['birth_date', '2020-01-01', 'before_or_equal'],
        'missing phone' => ['phone', '', 'required'],
        'malformed phone' => ['phone', 'not-a-phone', Phone::class],
    ]);

    it('expands a two-digit class year in the browser', function (string $input, string $expanded) {
        $this->travelTo(Carbon::create(2026));

        Route::livewire('/register/{showStep}', 'pages::auth.register')->middleware('web');

        visit('/register/auth.steps.details')
            ->fill('class_year', $input)
            ->keys('class_year', 'Tab')
            ->assertValue('class_year', $expanded)
            ->assertNoJavaScriptErrors();
    })->with([
        'this century' => ['15', '2015'],
        'a few years ahead' => [sprintf('%02d', (now()->year + 3) % 100), (string) (now()->year + 3)],
        'last century' => ['98', '1998'],
        'already four digits' => ['2015', '2015'],
    ]);
});

describe('password step', function () {
    beforeEach(function () {
        $this->step = $this->wizard->testStep('auth.steps.password');
    });

    it('advances once the step is completed', function () {
        $this->step
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatchedTo($this->wizard->instance()->getName(), 'nextStep');
    });

    it('rejects an unconfirmed password', function () {
        $this->step
            ->set('password', 'password')
            ->set('password_confirmation', 'different-password')
            ->call('save')
            ->assertHasErrors(['password' => 'confirmed'])
            ->assertNotDispatched('nextStep');
    });

});

describe('review step', function () {
    beforeEach(function () {
        $this->step = $this->wizard->testStep('auth.steps.review', $this->answers);
    });

    it('shows the answers for review', function () {
        $this->step
            ->assertSee('Test User')
            ->assertSee('test@example.com')
            ->assertSee('EPL/S 2015')
            ->assertSee('+33 6 12 34 56 78')
            ->assertDontSee('top-secret-password');
    });

    it('submits the answers to the wizard to create the account', function () {
        $this->step
            ->assertSee(__('auth.register.action'))
            ->call('nextStep')
            ->assertDispatchedTo($this->wizard->instance()->getName(), 'nextStep');
    });

    it('sends the member to the step that collects a field', function () {
        $this->step
            ->call('edit', 'gender')
            ->assertDispatchedTo($this->wizard->instance()->getName(), 'editField', field: 'gender');
    });
});
