<?php

namespace App\Actions\Mailcoach;

use App\Models\User;
use App\Services\Mailcoach\Subscriber;

readonly class SubscribeUserToListAction extends MailcoachAction
{
    public function __invoke(User $user, string $tag): void
    {
        $subscriber = $this->mailcoach->getSubscriber($user->email) ?? $this->createSubscriber($user);

        if ($subscriber) {
            $this->mailcoach->addTags($subscriber, [$tag]);
        }
    }

    protected function createSubscriber(User $user): ?Subscriber
    {
        return $this->mailcoach->subscribe(
            $user->email,
            $user->first_name,
            $user->last_name,
            [
                'class_course' => $user->class_course,
                'class_year' => $user->class_year,
            ],
            skipConfirmation: true,
        );
    }
}
