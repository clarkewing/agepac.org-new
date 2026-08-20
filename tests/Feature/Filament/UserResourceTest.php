<?php

use App\Enums\ClassCourse;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();

    $this->actingAs($this->admin);
});

test('users are listed', function () {
    $users = User::factory()->count(3)->create();

    Livewire::test(ListUsers::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($users);
});

test('users can be searched by name', function () {
    $user = User::factory()->create(['first_name' => 'Jean', 'last_name' => 'Mermoz']);
    $other = User::factory()->create(['first_name' => 'Antoine', 'last_name' => 'De Saint-Exupéry']);

    Livewire::test(ListUsers::class)
        ->searchTable('Mermoz')
        ->assertCanSeeTableRecords([$user])
        ->assertCanNotSeeTableRecords([$other]);
});

test('users can be filtered to approved only', function () {
    $approved = User::factory()->create();
    $unapproved = User::factory()->unapproved()->create();

    Livewire::test(ListUsers::class)
        ->filterTable('approved', true)
        ->assertCanSeeTableRecords([$approved])
        ->assertCanNotSeeTableRecords([$unapproved]);
});

test('users can be filtered to unapproved only', function () {
    $approved = User::factory()->create();
    $unapproved = User::factory()->unapproved()->create();

    Livewire::test(ListUsers::class)
        ->filterTable('approved', false)
        ->assertCanSeeTableRecords([$unapproved])
        ->assertCanNotSeeTableRecords([$approved]);
});

test('a blank approved filter leaves the query untouched', function () {
    $approved = User::factory()->create();
    $unapproved = User::factory()->unapproved()->create();

    Livewire::test(ListUsers::class)
        ->filterTable('approved', null)
        ->assertCanSeeTableRecords([$approved, $unapproved]);
});

test('users with a role are flagged in the table', function () {
    $user = User::factory()->create();

    Livewire::test(ListUsers::class)
        ->assertTableColumnExists(
            'name',
            fn (TextColumn $column): bool => $column->getIcon($column->getState()) !== null,
            $this->admin
        )
        ->assertTableColumnExists(
            'name',
            fn (TextColumn $column): bool => $column->getIcon($column->getState()) === null,
            $user,
        );
});

test('the class column uses the short course label', function () {
    $user = User::factory()->create(['class_course' => ClassCourse::PREPA_ATPL, 'class_year' => 2020]);

    Livewire::test(ListUsers::class)
        ->assertTableColumnStateSet('class', 'Prépa ATPL 2020', $user);
});

test('users can be filtered by class course', function () {
    $ept = User::factory()->create(['class_course' => ClassCourse::EPT]);
    $epl = User::factory()->create(['class_course' => ClassCourse::EPL_S]);

    Livewire::test(ListUsers::class)
        ->filterTable('class_course', 'EPT')
        ->assertCanSeeTableRecords([$ept])
        ->assertCanNotSeeTableRecords([$epl]);
});

test('users can be filtered by class year', function () {
    $veteran = User::factory()->create(['class_year' => 1985]);
    $rookie = User::factory()->create(['class_year' => 2024]);

    Livewire::test(ListUsers::class)
        ->filterTable('class_year', 1985)
        ->assertCanSeeTableRecords([$veteran])
        ->assertCanNotSeeTableRecords([$rookie]);
});

test('the class year filter only offers years with members', function () {
    $this->admin->update(['class_year' => 2015]);
    User::factory()->create(['class_year' => 2017]);

    Livewire::test(ListUsers::class)
        ->assertTableFilterExists('class_year', function (SelectFilter $filter): bool {
            $years = array_keys($filter->getOptions());

            return in_array(2015, $years, true)
                   && in_array(2017, $years, true)
                   && ! in_array(1949, $years, true);
        });
});

test('users can be filtered by role', function () {
    $user = User::factory()->create();

    Livewire::test(ListUsers::class)
        ->filterTable('role', 'admin')
        ->assertCanSeeTableRecords([$this->admin])
        ->assertCanNotSeeTableRecords([$user]);
});

test('a user can be edited', function () {
    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm([
            'first_name' => 'Hélène',
            'last_name' => 'Boucher',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->refresh())
        ->first_name->toBe('Hélène')
        ->last_name->toBe('Boucher');
});

test('certain fields are not editable', function () {
    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->assertFormFieldDoesNotExist('password')
        ->assertFormFieldDoesNotExist('stripe_id')
        ->assertFormFieldDoesNotExist('reputation')
        ->assertFormFieldDoesNotExist('bio');
});

test('EPL cannot be newly assigned as a class course', function () {
    $user = User::factory()->create(['class_course' => ClassCourse::EPL_S]);

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['class_course' => ClassCourse::EPL])
        ->call('save')
        ->assertHasFormErrors(['class_course']);

    expect($user->refresh()->class_course)->toBe(ClassCourse::EPL_S);
});

test('an existing EPL class course remains selectable', function () {
    $user = User::factory()->create(['class_course' => ClassCourse::EPL]);

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->refresh()->class_course)->toBe(ClassCourse::EPL);
});

test('class year must fall between 1949 and next year', function (int $year, bool $valid) {
    $user = User::factory()->create();

    $test = Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['class_year' => $year])
        ->call('save');

    $valid
        ? $test->assertHasNoFormErrors()
        : $test->assertHasFormErrors(['class_year']);
})->with([
    'before ENAC existed' => [1948, false],
    'ENAC founding year' => [1949, true],
    'next year' => [now()->year + 1, true],
    'beyond next year' => [now()->year + 2, false],
]);

test('significant timestamps are listed on the edit screen', function () {
    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->assertSchemaComponentExists('created_at')
        ->assertSchemaComponentExists('updated_at')
        ->assertSchemaComponentExists('email_verified_at')
        ->assertSchemaComponentExists('approved_at');
});

test('a password reset can be sent', function () {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->callAction(TestAction::make('sendPasswordReset')->schemaComponent('account::data::section'))
        ->assertHasNoFormErrors()
        ->assertNotified(__('admin.users.actions.send-password-reset.success'));

    Notification::assertSentTo($user, ResetPassword::class);
});

test('password reset links sent by admins are not throttled', function () {
    Notification::fake();

    $user = User::factory()->create();

    $component = Livewire::test(EditUser::class, ['record' => $user->getRouteKey()]);

    $component->callAction(TestAction::make('sendPasswordReset')->schemaComponent('account::data::section'));
    $component->callAction(TestAction::make('sendPasswordReset')->schemaComponent('account::data::section'));

    Notification::assertSentToTimes($user, ResetPassword::class, 2);
});

test('a verification email can be resent for unverified users', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->callAction(TestAction::make('resendVerification')->schemaComponent('email_verified_at'))
        ->assertHasNoFormErrors()
        ->assertNotified(__('admin.users.actions.resend-verification.success'));

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('the resend verification action is not available for verified users', function () {
    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->assertActionDoesNotExist(TestAction::make('resendVerification')->schemaComponent('email_verified_at'));
});

test('an unapproved user can be approved', function () {
    $user = User::factory()->unapproved()->create();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->callAction(TestAction::make('approve')->schemaComponent('approved_at'))
        ->assertHasNoFormErrors();

    expect($user->refresh()->approved_at)->not->toBeNull();
});

test('the approve action is not available for approved users', function () {
    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->assertActionDoesNotExist(TestAction::make('approve')->schemaComponent('approved_at'));
});

describe('header actions', function () {
    test('an unapproved user can be approved', function () {
        $user = User::factory()->unapproved()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->callAction('approve')
            ->assertHasNoFormErrors()
            ->assertNotified(__('admin.users.actions.approve.success'));

        expect($user->refresh()->approved_at)->not->toBeNull();
    });

    test('the approve action is hidden for approved users', function () {
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertActionHidden('approve');
    });

    test('a role can be assigned to a user', function () {
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->callAction('assignRole', data: ['role' => 'admin'])
            ->assertHasNoFormErrors()
            ->assertNotified(__('admin.users.actions.assign-role.success'));

        expect($user->refresh()->role)->toBe('admin');
    });

    test('a role can be removed from a user', function () {
        $user = User::factory()->admin()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->callAction('assignRole', data: ['role' => null])
            ->assertHasNoFormErrors();

        expect($user->refresh()->role)->toBeNull();
    });

    test('a role cannot be assigned to an unapproved user', function () {
        $user = User::factory()->unapproved()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertActionDisabled('assignRole');
    });

    test('admins cannot change their own role', function () {
        Livewire::test(EditUser::class, ['record' => $this->admin->getRouteKey()])
            ->assertActionDisabled('assignRole');
    });
});
