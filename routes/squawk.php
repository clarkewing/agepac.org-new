<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Membership;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware(['verified', 'handoff:/home'])
        ->name('dashboard');

    Route::prefix('settings')->group(function () {
        Route::get('profile', Profile::class)->name('settings.profile')->middleware('handoff:/account/info');
        Route::get('password', Password::class)->name('settings.password')->middleware('handoff:/account/info');
        Route::get('membership', Membership::class)->name('settings.membership');
        Route::get('appearance', Appearance::class)->name('settings.appearance');

        Route::redirect('/', 'settings/profile');
    });
});
