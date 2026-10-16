<?php

namespace App\Jobs;

use App\Actions\Mailcoach\SubscribeUserToListAction;
use App\Enums\MailcoachTag;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;

class SubscribeUserToNewsletter implements ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(public User $user) {}

    public function handle(SubscribeUserToListAction $subscribe): void
    {
        $subscribe($this->user, MailcoachTag::NEWSLETTER);
    }
}
