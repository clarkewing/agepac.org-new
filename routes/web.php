<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Membership;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'handoff:/home'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile')->middleware('handoff:/account/info');
    Route::get('settings/password', Password::class)->name('settings.password')->middleware('handoff:/account/info');
    Route::get('settings/membership', Membership::class)->name('settings.membership');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
