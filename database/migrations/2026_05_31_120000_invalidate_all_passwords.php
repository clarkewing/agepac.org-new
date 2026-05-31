<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Post-incident remediation.
     *
     * Mirrors members.agepac.org's invalidation migration. Sentinel value
     * is the same literal ('RESET_REQUIRED') so legacy_sync — which keeps
     * the password column in step across both databases via the new
     * members.UserObserver::updated() handler — sees a no-op when a member
     * who hasn't yet reset triggers a save.
     */
    private const SENTINEL = 'RESET_REQUIRED';

    public function up(): void
    {
        DB::table('users')
            ->whereNotNull('password')
            ->where('password', '!=', self::SENTINEL)
            ->update([
                'password' => self::SENTINEL,
                'remember_token' => null,
            ]);

        if (Schema::hasTable('password_reset_tokens')) {
            DB::table('password_reset_tokens')->truncate();
        }

        if (Schema::hasTable('sessions')) {
            DB::table('sessions')->truncate();
        }
    }

    public function down(): void
    {
        // No-op: invalidated passwords cannot be restored. Roll back by
        // restoring from a pre-incident database backup.
    }
};
