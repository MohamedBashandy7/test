<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UsersPolicy
{
    public function view(User $user, User $users): bool
    {
        return $user->isAdmin() || $user->isProjectManager();
    }

    public function create(User $user): bool
    {
          return $user->isAdmin() || $user->isProjectManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $users): bool
    {
        return $user->isAdmin() || ($user->isProjectManager() && $user->manager_id == $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $users): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $users): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $users): bool
    {
        return false;
    }
}
