<?php

namespace App\Console\Commands;

use App\Actions\Mailcoach\SubscribeUserToListAction;
use App\Actions\Mailcoach\UnsubscribeUserFromListAction;
use App\Models\User;
use Illuminate\Console\Command;

class SyncUsersToMailcoach extends Command
{
    const string NEWSLETTER_TAG = 'newsletter';

    const string MEMBERS_TAG = 'members_newsletter';

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
    protected $description = 'Sync all application users to Mailcoach with the proper tags based on membership status.';

    public function handle(): int
    {
        $processed = 0;
        $chunk = (int) $this->option('chunk');

        User::query()->orderBy('id')->chunkById($chunk, function ($users) use (&$processed) {
            foreach ($users as $user) {
                app(SubscribeUserToListAction::class)($user, self::NEWSLETTER_TAG);

                if ($this->hasActiveSubscription($user)) {
                    app(SubscribeUserToListAction::class)($user, self::MEMBERS_TAG);
                } else {
                    app(UnsubscribeUserFromListAction::class)($user, self::MEMBERS_TAG);
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
