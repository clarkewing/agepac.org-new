<?php

use App\Enums\ClassCourse;
use App\Models\User;
use Illuminate\Support\Carbon;
use Propaganistas\LaravelPhone\PhoneNumber;

/*
|--------------------------------------------------------------------------
| Instantiation & Factory
|--------------------------------------------------------------------------
*/

it('can be instantiated in unverified state', function () {
    $user = User::factory()->unverified()->make();

    expect($user->email_verified_at)->toBeNull();
});

it('can be instantiated in unapproved state', function () {
    $user = User::factory()->unapproved()->make();

    expect($user->approved_at)->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Attributes & Casting
|--------------------------------------------------------------------------
*/

it('has correct fillable fields', function () {
    expect((new User)->getFillable())->toBe([
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'class_course',
        'class_year',
        'gender',
        'birth_date',
        'phone',
        'avatar_path',
    ]);
});

it('has correct hidden fields', function () {
    expect((new User)->getHidden())->toBe([
        'password',
        'remember_token',
        'email_verified_at',
        'approved_at',
    ]);
});

it('casts attributes correctly', function () {
    expect(User::factory()->make())
        ->birth_date->toBeInstanceOf(Carbon::class)
        ->phone->toBeInstanceOf(PhoneNumber::class)
        ->class_course->toBeInstanceOf(ClassCourse::class)
        ->email_verified_at->toBeInstanceOf(Carbon::class)
        ->approved_at->toBeInstanceOf(Carbon::class);
});

/*
|--------------------------------------------------------------------------
| Mass Assignment
|--------------------------------------------------------------------------
*/

it('can mass assign all fillable attributes', function () {
    $user = new User([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'username' => 'johndoe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'class_course' => 'EPL/S',
        'class_year' => '2023',
        'gender' => 'M',
        'birth_date' => '1990-05-15',
        'phone' => '0669696969',
        'avatar_path' => 'avatars/john.jpg',
    ]);

    expect($user)
        ->first_name->toBe('John')
        ->last_name->toBe('Doe')
        ->username->toBe('johndoe')
        ->email->toBe('john@example.com')
        ->class_course->toBe(ClassCourse::EPL_S)
        ->class_year->toBe('2023')
        ->gender->toBe('M')
        ->birth_date->toDateString()->toBe('1990-05-15')
        ->phone->formatE164()->toBe('+33669696969')
        ->avatar_path->toBe('avatars/john.jpg');
});

/*
|--------------------------------------------------------------------------
| Approval
|--------------------------------------------------------------------------
*/

it('reports its approval state', function () {
    expect(User::factory()->make()->isApproved())->toBeTrue()
        ->and(User::factory()->unapproved()->make()->isApproved())->toBeFalse();
});

it('can be approved', function () {
    $user = User::factory()->unapproved()->create();

    $user->approve();

    expect($user->refresh()->isApproved())->toBeTrue();
});

it('scopes queries by approval state', function () {
    $approved = User::factory()->create();
    $unapproved = User::factory()->unapproved()->create();

    expect(User::approved()->pluck('id')->all())->toBe([$approved->id])
        ->and(User::unapproved()->pluck('id')->all())->toBe([$unapproved->id]);
});

/*
|--------------------------------------------------------------------------
| Roles & Permissions
|--------------------------------------------------------------------------
*/

it('cannot mass assign a role', function () {
    $user = new User(['role' => 'admin']);

    expect($user->role)->toBeNull();
});

it('grants the permissions of its role', function () {
    $admin = User::factory()->admin()->make();

    expect($admin->hasPermission('users:manage'))->toBeTrue()
        ->and($admin->hasPermission('forum:moderate'))->toBeFalse();
});

it('grants no permissions without a role', function () {
    $user = User::factory()->make();

    expect($user->hasPermission('users:manage'))->toBeFalse();
});

it('can be assigned and stripped of a role', function () {
    $user = User::factory()->create();

    $user->assignRole('admin');
    expect($user->refresh()->role)->toBe('admin');

    $user->assignRole(null);
    expect($user->refresh()->role)->toBeNull();
});

it('exposes role permissions through the gate', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    expect($admin->can('users:manage'))->toBeTrue()
        ->and($user->can('users:manage'))->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Accessors & Computed Attributes
|--------------------------------------------------------------------------
*/

it('uses username as route key', function () {
    expect((new User)->getRouteKeyName())->toBe('username');
});

it('returns full name from name attribute', function () {
    $user = User::factory()->make([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    expect($user->name)->toBe('John Doe');
});

it('returns class from class attribute', function () {
    $user = User::factory()->make([
        'class_course' => 'EPL/S',
        'class_year' => '2015',
    ]);

    expect($user->class)->toBe('EPL/S 2015');
});

it('uses the course label in the class attribute', function () {
    $user = User::factory()->make([
        'class_course' => 'Cursus Prépa ATPL',
        'class_year' => '2015',
    ]);

    expect($user->class)->toBe('Cycle Préparatoire ATPL 2015');
});

it('returns the short class', function () {
    $user = User::factory()->make([
        'class_course' => 'Cursus Prépa ATPL',
        'class_year' => '2015',
    ]);

    expect($user->shortClass())->toBe('Prépa ATPL 2015');
});

it('handles users without class information', function () {
    $user = User::factory()->make([
        'class_course' => null,
        'class_year' => null,
    ]);

    expect($user->class)->toBeNull()
        ->and($user->shortClass())->toBeNull();
});

it('returns correct initials', function () {
    expect(User::factory()->make(['first_name' => 'John', 'last_name' => 'Doe']))
        ->initials()->toBe('JD');

    expect(User::factory()->make(['first_name' => 'J', 'last_name' => 'D']))
        ->initials()->toBe('JD');
});

/*
|--------------------------------------------------------------------------
| Formatting
|--------------------------------------------------------------------------
*/

it('formats first name using name case', function () {
    expect(User::factory()->make(['first_name' => 'john']))
        ->first_name->toBe('John');
});

it('formats last name using name case', function () {
    expect(User::factory()->make(['last_name' => 'DOE']))
        ->last_name->toBe('Doe');
});

it('formats names with multiple words correctly', function () {
    expect(User::factory()->make([
        'first_name' => 'jean-michel',
        'last_name' => 'hons der berg',
    ]))
        ->first_name->toBe('Jean-Michel')
        ->last_name->toBe('Hons der Berg');
});

/*
|--------------------------------------------------------------------------
| Serialization & Visibility
|--------------------------------------------------------------------------
*/

it('hides sensitive attributes in array representation', function () {
    expect(User::factory()->make()->toArray())
        ->not->toHaveKey('password')
        ->not->toHaveKey('remember_token')
        ->not->toHaveKey('email_verified_at')
        ->not->toHaveKey('approved_at');
});
