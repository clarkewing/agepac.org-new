<?php

use Illuminate\Support\Facades\Route;

// Fortify registers the authentication routes itself (see config/fortify.php
// for the domain and middleware); only what Fortify does not provide lives here.

Route::middleware('guest')->group(function () {
    // Registration goes through the Livewire page rather than Fortify's
    // endpoint, so its feature is off, and the route is declared here.
    Route::livewire('register', 'pages::auth.register')->name('register');

    Route::livewire('recover', 'pages::auth.recover')->name('recovery');
    Route::livewire('recover/migrate', 'pages::auth.migrate')->name('recovery.migrate');
});
