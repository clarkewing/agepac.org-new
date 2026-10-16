<?php

use App\Models\User;
use App\Notifications\AccountApproved;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;

it('is sent on the queue', function () {
    Queue::fake();

    User::factory()->create()
        ->notify(new AccountApproved);

    Queue::assertPushed(SendQueuedNotifications::class);
});

it('welcomes the member and links to the members area', function () {
    $user = User::factory()->create(['first_name' => 'Jean']);

    $mail = (new AccountApproved)->toMail($user);

    expect($mail)
        ->subject->toBe(__('notifications.account-approved.subject'))
        ->greeting->toBe(__('notifications.account-approved.greeting', ['name' => 'Jean']))
        ->actionUrl->toBe(route('dashboard'));
});
