<?php

use App\Http\Middleware\EnsureUserIsApproved;
use ClarkeWing\Handoff\HandoffIntended;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->preventRequestForgery(except: [
            'stripe/*',
        ]);

        $middleware->alias([
            'approved' => EnsureUserIsApproved::class,
        ]);

        // A member already logged in here whom the legacy app sends to log in
        // goes straight back to the page they wanted there.
        $middleware->redirectUsersTo(function (Request $request): string {
            $intended = resolve(HandoffIntended::class);

            if ($request->routeIs('login') && $intended->remember($request->query('intended'))) {
                return $intended->pullUrl($request->user());
            }

            return route('dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
