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

        $isSoleTag = array_values($subscriber->tags) === [$tag];

        $this->mailcoachApi->removeTag($subscriber, $tag);

        if ($isSoleTag) {
            $this->mailcoachApi->unsubscribe($subscriber);
        }
    }
}
