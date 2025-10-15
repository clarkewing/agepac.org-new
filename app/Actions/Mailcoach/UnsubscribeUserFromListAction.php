<?php

namespace App\Actions\Mailcoach;

use App\Models\User;

readonly class UnsubscribeUserFromListAction extends MailcoachAction
{
    public function __invoke(User $user, string $tag): void
    {
        if (! $subscriber = $this->mailcoachApi->getSubscriber($user->email)) {
            return;
        }

        if (array_values($subscriber->tags) === [$tag]) {
            $this->mailcoachApi->unsubscribe($subscriber);
        } else {
            $this->mailcoachApi->removeTag($subscriber, $tag);
        }
    }
}
