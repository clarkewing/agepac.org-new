<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Http\Responses\Fortify\FailedPasswordResetLinkRequestResponse;
use App\Http\Responses\Fortify\LogoutResponse;
use App\Http\Responses\Fortify\PasswordResetResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse as FailedPasswordResetLinkRequestResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
        $this->app->bind(FailedPasswordResetLinkRequestResponseContract::class, FailedPasswordResetLinkRequestResponse::class);
        $this->app->bind(PasswordResetResponseContract::class, PasswordResetResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    protected function configureActions(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
    }

    /**
     * Configure Fortify views.
     */
    protected function configureViews(): void
    {
        Fortify::viewPrefix('pages.auth.');

        // Login is still served by the legacy app; drop this line at the flip.
        Fortify::loginView(fn () => redirect()->away(config('handoff.target_host').'/login'));
    }

    /**
     * Configure rate limiting.
     */
    protected function configureRateLimiting(): void
    {
        // Login is throttled by the EnsureLoginIsNotThrottled pipe instead.

        RateLimiter::for('two-factor', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request): Limit {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip(),
            );
        });
    }
}
