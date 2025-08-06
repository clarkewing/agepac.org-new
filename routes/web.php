<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Uri;
use Illuminate\View\Middleware\ShareErrorsFromSession;

$domain = Uri::of(config('app.url'))->host();

Route::domain($domain)
    // Prevent cookies on public site
    ->withoutMiddleware([
        StartSession::class,
        AuthenticateSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
    ])
    ->name('public.')
    ->group(__DIR__.'/public.php');

Route::domain('squawk.'.$domain)
    ->group(__DIR__.'/squawk.php');
