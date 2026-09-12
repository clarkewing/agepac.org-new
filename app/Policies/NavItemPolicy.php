<?php

namespace App\Policies;

use App\Models\NavItem;
use App\Models\User;

class NavItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('navigation:manage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('navigation:manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NavItem $navItem): bool
    {
        return $user->hasPermission('navigation:manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NavItem $navItem): bool
    {
        return $user->hasPermission('navigation:manage');
    }
}
