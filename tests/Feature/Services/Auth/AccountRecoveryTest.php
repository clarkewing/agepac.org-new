<?php

use App\Models\User;
use App\Services\Auth\AccountRecovery;
use Illuminate\Support\Facades\Session;

it('does not count another member’s verified email for the member recovering', function () {
    AccountRecovery::start(User::factory()->migrationRequired()->create());
    Session::put('email-verified', ['user_id' => User::factory()->create()->id, 'at' => now()->getTimestamp()]);

    expect(AccountRecovery::isVerified())->toBeFalse();
});

it('logs out devices remembered for the recovered account', function () {
    $user = User::factory()->create(['remember_token' => 'remembered-device']);

    AccountRecovery::complete($user, ['password' => 'new-password']);

    expect($user->fresh()->remember_token)->toBeNull();
});
