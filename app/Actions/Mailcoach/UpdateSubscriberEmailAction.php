<?php

namespace App\Actions\Mailcoach;

use App\Models\User;

readonly class UpdateSubscriberEmailAction extends MailcoachAction
{
    public function __invoke(User $user): void
    {
        $subscriber = $this->mailcoach->getSubscriber($user->getOriginal('email'));

        if ($subscriber) {
            $this->mailcoach->update($subscriber, ['email' => $user->email]);
        }
    }
}
