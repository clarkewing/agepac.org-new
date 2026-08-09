<?php

use App\Http\Middleware\SetLocaleFromSession;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Head\Enums\OgType;
use Laravel\Head\Enums\RobotsRule;
use Laravel\Head\Enums\TwitterCard;

$rootDomain = str(uri(config('app.url'))->host())->after('squawk.');

Route::domain($rootDomain)
    // Prevent cookies on public site
    ->withoutMiddleware([
        StartSession::class,
        AuthenticateSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class,
    ])
    ->withHead(
        title: ['value' => 'AGEPAC', 'suffix' => ' | AGEPAC', 'exact' => true],
        robots: RobotsRule::All,
        og: ['type' => OgType::Website, 'siteName' => 'AGEPAC', 'locale' => 'fr_FR'],
        twitter: ['card' => TwitterCard::SummaryWithLargeImage],
        themeColor: 'rgb(0, 10, 51)',
        manifest: '/manifest.webmanifest',
    )
    ->name('public.')
    ->group(__DIR__.'/public.php');

Route::domain($rootDomain->prepend('squawk.'))
    ->middleware(SetLocaleFromSession::class)
    ->group(__DIR__.'/squawk.php');
