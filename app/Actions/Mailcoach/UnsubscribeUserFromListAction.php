<?php

namespace App\Actions\Mailcoach;

use App\Models\User;

readonly class UnsubscribeUserFromListAction extends MailcoachAction
{
    public function __invoke(User $user, string $tag): void
    {
        if (! $subscriber = $this->mailcoach->getSubscriber($user->email)) {
            return;
        }

        $isSoleTag = array_values($subscriber->tags) === [$tag];

        $this->mailcoach->removeTag($subscriber, $tag);

        if ($isSoleTag) {
            $this->mailcoach->unsubscribe($subscriber);
        }
    }
}
