<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\AuthenticationException;

class PagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('pages:manage');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @throws AuthenticationException
     */
    public function view(?User $user, Page $page): Response
    {
        if ($user?->hasPermission('pages:manage')) {
            return Response::allow();
        }

        // Deny as a 404 so unpublished pages don't reveal their existence.
        if (! $page->isPublished()) {
            return Response::denyAsNotFound();
        }

        if ($page->restricted) {
            if ($user === null) {
                throw new AuthenticationException;
            }

            return $user->isApproved() ? Response::allow() : Response::deny();
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('pages:manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Page $page): bool
    {
        return $user->hasPermission('pages:manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Page $page): bool
    {
        return $user->hasPermission('pages:manage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Page $page): bool
    {
        return $user->hasPermission('pages:manage');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Page $page): bool
    {
        return false;
    }
}
