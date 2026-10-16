<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Accounts imported from the legacy app without a password (null) are
     * sent through the migration wizard at login. Null is kept for accounts
     * that sign in with a passkey only.
     *
     * A bulk update fires no model events, so nothing syncs the sentinel
     * back to the legacy app, where null still means an unmigrated account.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNull('password')
            ->update(['password' => User::MIGRATION_SENTINEL]);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('password', User::MIGRATION_SENTINEL)
            ->update(['password' => null]);
    }
};
