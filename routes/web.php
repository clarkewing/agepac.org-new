<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

$rootDomain = str(uri(config('app.url'))->host())->after('squawk.');

Route::domain($rootDomain)
    // Prevent cookies on public site
    ->withoutMiddleware([
        StartSession::class,
        AuthenticateSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
    ])
    ->name('public.')
    ->group(__DIR__.'/public.php');

Route::domain($rootDomain->prepend('squawk.'))
    ->group(__DIR__.'/squawk.php');
