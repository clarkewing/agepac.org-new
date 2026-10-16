<?php

use App\Actions\ApproveUser;
use App\Models\User;
use App\Notifications\AccountApproved;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->action = resolve(ApproveUser::class);
});

it('approves the member and emails them', function () {
    Notification::fake();
    $this->freezeSecond();
    $user = User::factory()->pendingApproval()->create();

    ($this->action)($user);

    expect($user->fresh()->approved_at)->toEqual(now());
    Notification::assertSentTo($user, AccountApproved::class);
});
