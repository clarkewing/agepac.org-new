<?php

namespace App\Jobs\Mailcoach;

use App\Enums\MailcoachTag;
use App\Models\User;
use App\Services\Mailcoach\MailcoachApi;
use App\Services\Mailcoach\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\DeleteWhenMissingModels;

/**
 * Tags a user's subscriber, subscribing them first if they are not yet.
 */
#[DeleteWhenMissingModels]
class SubscribeToList implements ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(public User $user, public MailcoachTag $tag) {}

    public function handle(MailcoachApi $mailcoach): void
    {
        $subscriber = $mailcoach->getSubscriber($this->user->email) ??
            $mailcoach->subscribe(
                $this->user->email,
                $this->user->first_name,
                $this->user->last_name,
                [
                    'class_course' => $this->user->class_course?->value,
                    'class_year' => $this->user->class_year,
                ],
                skipConfirmation: true,
            );

        if ($subscriber) {
            $mailcoach->addTags($subscriber, [$this->tag->value]);
        }
    }
}
