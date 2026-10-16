<?php

namespace App\Models\Concerns;

use App\Services\Authorization\Role;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasRole
{
    public function getRole(): ?Role
    {
        return Role::find($this->role);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->getRole()?->hasPermission($permission) ?? false;
    }

    /**
     * @param  list<string>  $permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return array_any($permissions, fn (string $permission) => $this->hasPermission($permission));
    }

    /**
     * Users whose role grants the permission.
     */
    #[Scope]
    protected function withPermission(Builder $query, string $permission): void
    {
        $query->whereIn('role', array_keys(array_filter(
            Role::all(),
            fn (Role $role): bool => $role->hasPermission($permission),
        )));
    }

    public function assignRole(?string $role): bool
    {
        return $this->forceFill([
            'role' => $role,
        ])->save();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can('access-admin-panel');
    }
}
