<?php

namespace App\Providers;

use App\Actions\Fortify\ConfirmPassword;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Http\Middleware\RedirectIfRecoveryRequired;
use App\Http\Responses\Fortify\EmailVerificationNotificationSentResponse;
use App\Http\Responses\Fortify\FailedPasswordResetLinkRequestResponse;
use App\Http\Responses\Fortify\LockoutResponse;
use App\Http\Responses\Fortify\LogoutResponse;
use App\Http\Responses\Fortify\PasswordResetResponse;
use App\Services\Auth\ProgressiveLoginRateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\EmailVerificationNotificationSentResponse as EmailVerificationNotificationSentResponseContract;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse as FailedPasswordResetLinkRequestResponseContract;
use Laravel\Fortify\Contracts\LockoutResponse as LockoutResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Lockouts grow with each failure past the fifth instead of resetting every minute.
        $this->app->bind(LoginRateLimiter::class, ProgressiveLoginRateLimiter::class);

        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
        $this->app->singleton(EmailVerificationNotificationSentResponseContract::class, EmailVerificationNotificationSentResponse::class);
        $this->app->singleton(LockoutResponseContract::class, LockoutResponse::class);
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
        $this->configureAuthenticationPipeline();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    protected function configureActions(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::confirmPasswordsUsing($this->app->make(ConfirmPassword::class));
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
     * Configure the authentication pipeline.
     */
    protected function configureAuthenticationPipeline(): void
    {
        // Fortify's default pipeline, plus RedirectIfRecoveryRequired:
        // a sentinel password must never reach the bcrypt hasher.
        Fortify::authenticateThrough(fn (Request $request): array => array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            CanonicalizeUsername::class,
            RedirectIfRecoveryRequired::class,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
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

        // One resend a minute per member; the emailed link itself may be opened more freely.
        RateLimiter::for('verification', function (Request $request): Limit {
            return $request->routeIs('verification.send')
                ? Limit::perMinute(1)->by($request->user()->getKey())
                : Limit::perMinute(6)->by($request->user()->getKey());
        });

        RateLimiter::for('passkeys', function (Request $request): Limit {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip(),
            );
        });
    }
}
