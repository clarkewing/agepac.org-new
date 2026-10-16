<?php

namespace App\Jobs\Mailcoach;

use App\Enums\MailcoachTag;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\DeleteWhenMissingModels;

/**
 * Puts a member on the members newsletter, or takes them off it, according
 * to their membership when the job runs rather than when it was queued, so
 * a burst of subscription updates settles on the latest state.
 */
#[DeleteWhenMissingModels]
class UpdateMembersNewsletterSubscription implements ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(public User $user) {}

    public function handle(): void
    {
        $hasActiveMembership = $this->user->subscriptions()->where('type', 'membership')->active()->exists();

        if ($hasActiveMembership) {
            SubscribeToList::dispatchSync($this->user, MailcoachTag::MEMBERS_NEWSLETTER);
        } else {
            UnsubscribeFromList::dispatchSync($this->user->email, MailcoachTag::MEMBERS_NEWSLETTER);
        }
    }
}
