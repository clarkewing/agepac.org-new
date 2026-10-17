<?php

use App\Http\Controllers\UserInfoController;
use Illuminate\Support\Facades\Route;

// Passport's OAuth server, registered here rather than by Passport so it only
// answers on the members host (see AuthServiceProvider).
Route::as('passport.')
    ->prefix('oauth')
    ->namespace('Laravel\Passport\Http\Controllers')
    ->group(base_path('vendor/laravel/passport/routes/web.php'));

Route::get('oauth/userinfo', UserInfoController::class)
    ->middleware('auth:api')
    ->name('oauth.userinfo');
