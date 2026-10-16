<?php

use App\Actions\ApproveUser;
use App\Enums\MailcoachTag;
use App\Jobs\Mailcoach\SubscribeToList;
use App\Models\User;
use App\Notifications\AccountApproved;
use App\Services\Mailcoach\Facades\Mailcoach;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

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

it('signs the member up to the newsletter', function () {
    $user = User::factory()->pendingApproval()->create();

    ($this->action)($user);

    expect(Mailcoach::getSubscriber($user->email))
        ->not->toBeNull()
        ->tags->toContain('newsletter');
});

it('signs the member up to the newsletter on the queue', function () {
    Queue::fake();
    $user = User::factory()->pendingApproval()->create();

    ($this->action)($user);

    Queue::assertPushed(SubscribeToList::class,
        fn (SubscribeToList $job): bool => $job->user->is($user) && $job->tag === MailcoachTag::NEWSLETTER
    );
});
