<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Pluralizer;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('locale')) {
            app()->setLocale($request->session()->get('locale'));
        } else {
            app()->setLocale(config('app.locale'));
        }

        Pluralizer::useLanguage(match (app()->getLocale()) {
            'fr' => 'french',
            default => 'english',
        });

        return $next($request);
    }
}
