<?php

use App\Models\User;
use App\Notifications\UserPendingApproval;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;

it('tells everyone who can approve accounts about a new one', function () {
    Notification::fake();
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $user = User::factory()->pendingApproval()->create();

    event(new Registered($user));

    Notification::assertSentTo($admin, UserPendingApproval::class,
        fn (UserPendingApproval $notification): bool => $notification->user->is($user)
    );
    Notification::assertNotSentTo($member, UserPendingApproval::class);
});

it('stays quiet about an account registered already approved', function () {
    Notification::fake();
    $admin = User::factory()->admin()->create();

    event(new Registered(User::factory()->create()));

    Notification::assertNotSentTo($admin, UserPendingApproval::class);
});
