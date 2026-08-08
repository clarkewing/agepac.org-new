<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * No-op on fresh databases, where the create migrations already
     * produce this schema.
     */
    public function up(): void
    {
        Schema::table('cache', function (Blueprint $table) {
            $table->bigInteger('expiration')->change();
        });

        if (! Schema::hasIndex('cache', ['expiration'])) {
            Schema::table('cache', function (Blueprint $table) {
                $table->index('expiration');
            });
        }

        Schema::table('cache_locks', function (Blueprint $table) {
            $table->bigInteger('expiration')->change();
        });

        if (! Schema::hasIndex('cache_locks', ['expiration'])) {
            Schema::table('cache_locks', function (Blueprint $table) {
                $table->index('expiration');
            });
        }

        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedSmallInteger('attempts')->change();
        });

        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->string('connection')->change();
            $table->string('queue')->change();
        });

        if (! Schema::hasIndex('failed_jobs', ['connection', 'queue', 'failed_at'])) {
            Schema::table('failed_jobs', function (Blueprint $table) {
                $table->index(['connection', 'queue', 'failed_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('failed_jobs', ['connection', 'queue', 'failed_at'])) {
            Schema::table('failed_jobs', function (Blueprint $table) {
                $table->dropIndex(['connection', 'queue', 'failed_at']);
            });
        }

        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->text('connection')->change();
            $table->text('queue')->change();
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedTinyInteger('attempts')->change();
        });

        if (Schema::hasIndex('cache_locks', ['expiration'])) {
            Schema::table('cache_locks', function (Blueprint $table) {
                $table->dropIndex(['expiration']);
            });
        }

        Schema::table('cache_locks', function (Blueprint $table) {
            $table->integer('expiration')->change();
        });

        if (Schema::hasIndex('cache', ['expiration'])) {
            Schema::table('cache', function (Blueprint $table) {
                $table->dropIndex(['expiration']);
            });
        }

        Schema::table('cache', function (Blueprint $table) {
            $table->integer('expiration')->change();
        });
    }
};
