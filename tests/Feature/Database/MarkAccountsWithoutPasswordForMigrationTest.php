<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

it('marks accounts without a password for migration, and only those', function () {
    $imported = User::factory()->create();
    DB::table('users')->where('id', $imported->id)->update(['password' => null]);
    $member = User::factory()->create();
    $resetRequired = User::factory()->passwordResetRequired()->create();

    (require database_path('migrations/2026_10_16_142331_mark_accounts_without_password_for_migration.php'))->up();

    expect($imported->fresh()->requiresMigration())->toBeTrue()
        ->and($member->fresh()->password)->toBe($member->password)
        ->and($resetRequired->fresh()->requiresPasswordReset())->toBeTrue();
});
