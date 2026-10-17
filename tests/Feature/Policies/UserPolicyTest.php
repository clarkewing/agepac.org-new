<?php

use App\Models\User;

it('lets users who can approve accounts approve one pending approval', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->can('approve', User::factory()->pendingApproval()->create()))->toBeTrue()
        ->and($admin->can('approve', User::factory()->create()))->toBeFalse();
});

it('forbids members without the permission from approving accounts', function () {
    expect(User::factory()->create()->can('approve', User::factory()->pendingApproval()->create()))->toBeFalse();
});

it('lets users who can approve accounts reject one pending approval', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->can('delete', User::factory()->pendingApproval()->create()))->toBeTrue()
        ->and($admin->can('delete', User::factory()->create()))->toBeFalse();
});

it('keeps an account with a membership on record from being rejected', function () {
    $user = User::factory()->pendingApproval()->withMembership()->create();

    expect(User::factory()->admin()->create()->can('delete', $user))->toBeFalse();
});

it('forbids members without the permission from rejecting accounts', function () {
    expect(User::factory()->create()->can('delete', User::factory()->pendingApproval()->create()))->toBeFalse();
});
