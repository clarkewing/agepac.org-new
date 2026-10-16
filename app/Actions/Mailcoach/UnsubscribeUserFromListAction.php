<?php

namespace App\Actions\Mailcoach;

use App\Enums\MailcoachTag;
use App\Models\User;

readonly class UnsubscribeUserFromListAction extends MailcoachAction
{
    public function __invoke(User $user, MailcoachTag $tag): void
    {
        if (! $subscriber = $this->mailcoach->getSubscriber($user->email)) {
            return;
        }

        $isSoleTag = array_values($subscriber->tags) === [$tag->value];

        $this->mailcoach->removeTag($subscriber, $tag->value);

        if ($isSoleTag) {
            $this->mailcoach->unsubscribe($subscriber);
        }
    }
}
