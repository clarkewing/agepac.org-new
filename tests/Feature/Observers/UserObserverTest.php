<?php

use App\Actions\Mailcoach\SubscribeUserToListAction;
use App\Enums\MailcoachTag;
use App\Models\User;
use App\Services\Mailcoach\Facades\Mailcoach;

it('leaves a newly created user off the newsletter until approved', function () {
    $user = User::factory()->pendingApproval()->create();

    expect(Mailcoach::getSubscriber($user->email))->toBeNull();
});

it('updates the subscriber email when the user email changes', function () {
    resolve(SubscribeUserToListAction::class)($user = User::factory()->create(['email' => 'old@example.com']), MailcoachTag::NEWSLETTER);

    $user->update(['email' => 'new@example.com']);

    expect(Mailcoach::getSubscriber('old@example.com'))->toBeNull();
    expect(Mailcoach::getSubscriber('new@example.com'))->not->toBeNull();
});

it('unsubscribes the user from the newsletter list on delete', function () {
    resolve(SubscribeUserToListAction::class)($user = User::factory()->create(), MailcoachTag::NEWSLETTER);

    $user->delete();

    expect(Mailcoach::getSubscriber($user->email))
        ->not->toBeNull()
        ->tags->not->toContain('newsletter')
        ->unsubscribedAt->not->toBeNull();
});
