<?php

use App\Enums\MailcoachTag;
use App\Jobs\Mailcoach\SubscribeToList;
use App\Jobs\Mailcoach\UnsubscribeFromList;
use App\Jobs\Mailcoach\UpdateSubscriberEmail;
use App\Models\User;
use App\Services\Mailcoach\Facades\Mailcoach;
use Illuminate\Support\Facades\Queue;

it('leaves a newly created user off the newsletter until approved', function () {
    $user = User::factory()->pendingApproval()->create();

    expect(Mailcoach::getSubscriber($user->email))->toBeNull();
});

it('updates the subscriber email when the user email changes', function () {
    SubscribeToList::dispatchSync($user = User::factory()->create(['email' => 'old@example.com']), MailcoachTag::NEWSLETTER);

    $user->update(['email' => 'new@example.com']);

    expect(Mailcoach::getSubscriber('old@example.com'))->toBeNull();
    expect(Mailcoach::getSubscriber('new@example.com'))->not->toBeNull();
});

it('updates the subscriber email on the queue', function () {
    Queue::fake();
    $user = User::factory()->create(['email' => 'old@example.com']);

    $user->update(['email' => 'new@example.com']);

    Queue::assertPushed(UpdateSubscriberEmail::class,
        fn (UpdateSubscriberEmail $job): bool => $job->from === 'old@example.com' && $job->to === 'new@example.com'
    );
});

it('unsubscribes the user from the newsletter list on delete', function () {
    SubscribeToList::dispatchSync($user = User::factory()->create(), MailcoachTag::NEWSLETTER);

    $user->delete();

    expect(Mailcoach::getSubscriber($user->email))
        ->not->toBeNull()
        ->tags->not->toContain('newsletter')
        ->unsubscribedAt->not->toBeNull();
});

it('unsubscribes a deleted user on the queue', function () {
    Queue::fake();
    $user = User::factory()->create();

    $user->delete();

    Queue::assertPushed(UnsubscribeFromList::class,
        fn (UnsubscribeFromList $job): bool => $job->email === $user->email && $job->tag === MailcoachTag::NEWSLETTER
    );
});
