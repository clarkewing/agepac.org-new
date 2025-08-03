<?php

namespace Tests\Helpers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LegacySyncHelpers
{
    /**
     * Setup legacy database with in-memory SQLite
     */
    public static function setupLegacyDatabase(): void
    {
        config()->set('database.connections.legacy', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Purge and reconnect to force Laravel to use the new config
        DB::purge('legacy');
        DB::reconnect('legacy');

        Schema::connection('legacy')->create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('class_course', 30)->nullable();
            $table->year('class_year')->nullable();
            $table->string('gender', 1);
            $table->date('birthdate')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('avatar_path')->nullable();
            $table->unsignedInteger('reputation')->default(0);
            $table->text('bio')->nullable();
            $table->unsignedMediumInteger('flight_hours')->nullable();
            $table->rememberToken();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->string('stripe_id')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
        });

        Schema::connection('legacy')->create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('name');
            $table->string('stripe_id');
            $table->string('stripe_status');
            $table->string('stripe_plan')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::connection('legacy')->create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id');
            $table->string('stripe_id');
            $table->string('stripe_plan');
            $table->integer('quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Setup legacy database and verify sync between databases
     *
     * @param  string  $table  The table name to check
     * @param  int  $id  The record ID to check
     * @param  array  $expectedMappings  Key-value pairs of expected column mappings to verify
     */
    public static function verifyLegacySync(string $table, int $id, array $expectedMappings = []): void
    {
        $legacyRecord = DB::connection('legacy')
            ->table($table)
            ->where('id', $id)
            ->first();

        expect($legacyRecord)->not->toBeNull();

        foreach ($expectedMappings as $column => $value) {
            expect($legacyRecord->{$column})->toBe($value);
        }
    }
}
