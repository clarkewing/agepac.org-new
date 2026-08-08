<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware(['verified', 'handoff:/home'])
        ->name('dashboard');

    Route::prefix('settings')->group(function () {
        Route::livewire('profile', 'pages::settings.profile')->name('settings.profile')->middleware('handoff:/account/info');
        Route::livewire('password', 'pages::settings.password')->name('settings.password')->middleware('handoff:/account/info');
        Route::livewire('membership', 'pages::settings.membership')->name('settings.membership');
        Route::livewire('appearance', 'pages::settings.appearance')->name('settings.appearance');

        Route::redirect('/', 'settings/profile');
    });
});
