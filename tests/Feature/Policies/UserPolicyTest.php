<?php

use App\Models\User;
use Laravel\Cashier\Subscription;

it('lets users who can approve accounts approve one pending approval', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->can('approve', User::factory()->pendingApproval()->create()))->toBeTrue()
        ->and($admin->can('approve', User::factory()->create()))->toBeFalse();
});

it('forbids members without the permission from approving accounts', function () {
    expect(User::factory()->create()->can('approve', User::factory()->pendingApproval()->create()))->toBeFalse();
});
