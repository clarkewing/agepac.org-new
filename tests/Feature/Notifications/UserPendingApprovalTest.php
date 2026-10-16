<?php

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Notifications\UserPendingApproval;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;

it('is sent on the queue', function () {
    Queue::fake();

    User::factory()->admin()->create()
        ->notify(new UserPendingApproval(User::factory()->pendingApproval()->create()));

    Queue::assertPushed(SendQueuedNotifications::class);
});

it('names the new member and links to their account in the admin panel', function () {
    $user = User::factory()->pendingApproval()->create([
        'first_name' => 'Jean',
        'last_name' => 'Mermoz',
        'class_course' => 'EPL/S',
        'class_year' => 2015,
    ]);

    $mail = new UserPendingApproval($user)->toMail(User::factory()->admin()->create());

    expect($mail)
        ->subject->toBe(__('notifications.user-pending-approval.subject'))
        ->introLines->toContain(__('notifications.user-pending-approval.intro', ['name' => 'Jean Mermoz', 'class' => $user->class]))
        ->actionUrl->toBe(UserResource::getUrl('edit', ['record' => $user]));
});

it('escapes the new member’s name', function () {
    $user = User::factory()->pendingApproval()->create([
        'first_name' => "<script>alert('xss')</script>",
    ]);

    $content = (string) (new UserPendingApproval($user))->toMail(User::factory()->admin()->create())->render();

    expect($content)
        ->toContain('&lt;script&gt;')
        ->not->toContain("<script>alert('xss')</script>");
});
