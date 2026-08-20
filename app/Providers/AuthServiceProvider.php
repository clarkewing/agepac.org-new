<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Authorization\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureRoles();
        $this->configurePermissions();
    }

    protected function configureRoles(): void
    {
        Role::define('admin', [
            'users:manage',
        ]);
    }

    protected function configurePermissions(): void
    {
        // Every permission granted by a user's role is available as a Gate
        // ability; other abilities fall through to gates and policies as usual.
        Gate::before(fn (User $user, string $ability): ?bool => $user->hasPermission($ability) ? true : null);

        Gate::define('access-admin-panel', fn (User $user): bool => $user->hasAnyPermission($this->adminPanelPermissions));
    }
}
