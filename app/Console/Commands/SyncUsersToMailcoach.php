<?php

namespace App\Console\Commands;

use App\Actions\Mailcoach\SubscribeUserToListAction;
use App\Actions\Mailcoach\UnsubscribeUserFromListAction;
use App\Enums\MailcoachTag;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SyncUsersToMailcoach extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailcoach:sync-users {--chunk=100 : Number of users to process per chunk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all approved users to Mailcoach with the proper tags based on membership status.';

    public function handle(): int
    {
        $processed = 0;
        $chunk = (int) $this->option('chunk');

        User::query()
            ->approved()
            ->orderBy('id')
            ->chunkById($chunk, function (Collection $users) use (&$processed) {
                foreach ($users as $user) {
                    app(SubscribeUserToListAction::class)($user, MailcoachTag::NEWSLETTER);

                    if ($this->hasActiveSubscription($user)) {
                        app(SubscribeUserToListAction::class)($user, MailcoachTag::MEMBERS_NEWSLETTER);
                    } else {
                        app(UnsubscribeUserFromListAction::class)($user, MailcoachTag::MEMBERS_NEWSLETTER);
                    }

                    $processed++;
                }
            });

        $this->info("Processed {$processed} users.");

        return self::SUCCESS;
    }

    protected function hasActiveSubscription(User $user): bool
    {
        return $user->subscriptions()
            ->where('type', 'membership')
            ->active()
            ->exists();
    }
}
