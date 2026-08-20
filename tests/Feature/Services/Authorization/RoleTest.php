<?php

use App\Services\Authorization\Role;

it('finds defined roles by key', function () {
    expect(Role::find('admin'))->toBeInstanceOf(Role::class)
        ->and(Role::find('nonexistent'))->toBeNull()
        ->and(Role::find(null))->toBeNull();
});

it('lists all defined roles', function () {
    expect(Role::all())->toHaveKey('admin');
});

it('knows which permissions it grants', function () {
    expect(Role::find('admin'))
        ->hasPermission('users:manage')->toBeTrue()
        ->hasPermission('forum:moderate')->toBeFalse();
});

it('translates its name and description for the current locale', function () {
    $role = Role::find('admin');

    app()->setLocale('en');
    expect($role->name)->toBe('Administrator')
        ->and($role->description)->toBe('Full access to manage members in the admin area.');

    app()->setLocale('fr');
    expect($role->name)->toBe('Administrateur')
        ->and($role->description)->toBe('Gestion complète des membres dans l’espace d’administration.');
});
