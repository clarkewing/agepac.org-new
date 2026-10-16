<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Auth\AccountRecovery;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

/**
 * Diverts accounts whose stored password is a sentinel (the post-incident
 * reset, or an imported account that never set a password) to the
 * recovery wizard. Used both as a pipe in Fortify's login pipeline, where
 * it runs before any attempt reaches the bcrypt hasher, and as route
 * middleware on the password reset link request.
 */
class RedirectIfRecoveryRequired
{
    /**
     * Specify that only imported accounts should be diverted, letting a
     * post-incident reset go through the ordinary reset link.
     */
    public static function migrationOnly(): string
    {
        return static::class.':migration';
    }

    /**
     * @param  Closure(Request): mixed  $next
     * @param  'migration'|null  $only  See migrationOnly().
     */
    public function handle(Request $request, Closure $next, ?string $only = null): mixed
    {
        if (blank($email = $request->input(Fortify::email()))) {
            return $next($request);
        }

        $user = User::query()
            ->where('email', Str::lower((string) $email))
            ->first();

        $requiresRecovery = match ($only) {
            'migration' => $user?->requiresMigration(),
            default => $user?->requiresRecovery(),
        };

        if ($requiresRecovery) {
            AccountRecovery::start($user);

            return redirect()->route('recovery');
        }

        return $next($request);
    }
}
