<?php

namespace App\Policies;

use App\Models\User;
use Datlechin\FilamentMenuBuilder\Models\Menu;

class MenuPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('navigation:manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Menu $menu): bool
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
    public function update(User $user, Menu $menu): bool
    {
        return $user->hasPermission('navigation:manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Menu $menu): bool
    {
        return $user->hasPermission('navigation:manage');
    }

    /**
     * Determine whether the user can bulk-delete models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('navigation:manage');
    }
}
