<?php

use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

// Outside the auth group: unrestricted pages are viewable by anyone, and
// the policy handles restricted and unpublished pages per request.
Route::get('pages/{page:path}', [PagesController::class, 'show'])
    ->where('page', '.+')
    ->name('pages.show');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware(['verified', 'handoff:/home'])
        ->name('dashboard');

    Route::get('attachments/{attachment}/{name?}', [AttachmentsController::class, 'show'])
        ->where('name', '[a-z0-9._-]+')
        ->middleware('approved')
        ->name('attachments.show');

    Route::prefix('settings')->group(function () {
        Route::livewire('profile', 'pages::settings.profile')->name('settings.profile')->middleware('handoff:/account/info');
        Route::livewire('password', 'pages::settings.password')->name('settings.password')->middleware('handoff:/account/info');
        Route::livewire('membership', 'pages::settings.membership')->name('settings.membership');
        Route::livewire('appearance', 'pages::settings.appearance')->name('settings.appearance');

        Route::redirect('/', 'settings/profile');
    });
});
