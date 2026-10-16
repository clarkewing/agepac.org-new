<?php

namespace App\Console\Commands;

use App\Enums\MailcoachTag;
use App\Jobs\Mailcoach\SubscribeToList;
use App\Jobs\Mailcoach\UpdateMembersNewsletterSubscription;
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
                    SubscribeToList::dispatchSync($user, MailcoachTag::NEWSLETTER);
                    UpdateMembersNewsletterSubscription::dispatchSync($user);

                    $processed++;
                }
            });

        $this->info("Processed {$processed} users.");

        return self::SUCCESS;
    }
}
