<?php

namespace App\Providers;

use App\Models\Passport\Client;
use App\Models\User;
use App\Services\Authorization\Role;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Permissions which are exercised through the admin panel. A role granting
     * any of these needs access to the panel itself.
     *
     * @var list<string>
     */
    protected array $adminPanelPermissions = [
        'users:manage',
        'pages:manage',
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureRoles();
        $this->configurePermissions();
        $this->configurePassport();
    }

    protected function configureRoles(): void
    {
        Role::define('admin', [
            'users:manage',
            'users:approve',
            'pages:manage',
            'perfos:admin',
            'escales:admin',
        ]);
    }

    protected function configurePermissions(): void
    {
        // Every permission granted by a user's role is available as a Gate
        // ability; other abilities fall through to gates and policies as usual.
        Gate::before(fn (User $user, string $ability): ?bool => $user->hasPermission($ability) ? true : null);

        Gate::define('access-admin-panel', fn (User $user): bool => $user->hasAnyPermission($this->adminPanelPermissions));
    }

    protected function configurePassport(): void
    {
        Passport::useClientModel(Client::class);

        Passport::$deviceCodeGrantEnabled = false;

        Passport::tokensExpireIn(CarbonInterval::hour());
        Passport::refreshTokensExpireIn(CarbonInterval::days(30));

        Passport::authorizationView('pages.auth.authorize');
    }
}
