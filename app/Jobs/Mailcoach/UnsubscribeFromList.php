<?php

namespace App\Jobs\Mailcoach;

use App\Enums\MailcoachTag;
use App\Services\Mailcoach\MailcoachApi;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Removes a tag from a subscriber, unsubscribing them when it was their
 * last. Carries the email address rather than the user, who may be deleted
 * by the time the job runs.
 */
class UnsubscribeFromList implements ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(public string $email, public MailcoachTag $tag) {}

    public function handle(MailcoachApi $mailcoach): void
    {
        if (! $subscriber = $mailcoach->getSubscriber($this->email)) {
            return;
        }

        $isSoleTag = array_values($subscriber->tags) === [$this->tag->value];

        $mailcoach->removeTag($subscriber, $this->tag->value);

        if ($isSoleTag) {
            $mailcoach->unsubscribe($subscriber);
        }
    }
}
