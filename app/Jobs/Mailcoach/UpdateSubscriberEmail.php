<?php

namespace App\Jobs\Mailcoach;

use App\Services\Mailcoach\MailcoachApi;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;

class UpdateSubscriberEmail implements ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(public string $from, public string $to) {}

    public function handle(MailcoachApi $mailcoach): void
    {
        if ($subscriber = $mailcoach->getSubscriber($this->from)) {
            $mailcoach->update($subscriber, ['email' => $this->to]);
        }
    }
}
