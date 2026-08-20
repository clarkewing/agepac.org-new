<?php

use App\Services\Mailcoach\Facades\Mailcoach;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const string OLD_VALUE = 'Cursus Prépa ATPL';

    private const string NEW_VALUE = 'Cycle Préparatoire ATPL';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('class_course', self::OLD_VALUE)
            ->update(['class_course' => self::NEW_VALUE]);

        $this->updateMailcoachSubscribers(self::NEW_VALUE);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('class_course', self::NEW_VALUE)
            ->update(['class_course' => self::OLD_VALUE]);

        $this->updateMailcoachSubscribers(self::OLD_VALUE);
    }

    /**
     * Push the renamed course to the affected users' Mailcoach subscribers.
     */
    private function updateMailcoachSubscribers(string $value): void
    {
        // Mailcoach is not configured in every environment (e.g. testing,
        // where this migration runs on every database refresh).
        if (blank(config('services.mailcoach.url'))) {
            return;
        }

        DB::table('users')
            ->where('class_course', $value)
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($value): void {
                foreach ($users as $user) {
                    if ($subscriber = Mailcoach::getSubscriber($user->email)) {
                        Mailcoach::update($subscriber, ['class_course' => $value]);
                    }
                }
            });
    }
};
