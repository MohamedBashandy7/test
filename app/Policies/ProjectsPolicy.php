<?php

namespace App\Policies;

use App\Models\Projects;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectsPolicy {
    public function viewAny(User $user): bool {
        return true; // يمكن للجميع رؤية قائمة المشاريع
    }

    public function view(User $user): bool {
        return true;
    }

    public function create(User $user) {
        return $user->isAdmin() || $user->isProjectManager();
        // ? Response::allow()
        // : Response::deny('Only admins or project managers can create projects.');
    }

    public function update(User $user): bool {
        return $user->isAdmin() || $user->isProjectManager();
    }

    public function delete(User $user): bool {
        return $user->isAdmin() || $user->isProjectManager();
    }

    public function assignProjectManager(User $user): bool {
        return $user->isAdmin();
    }

    public function approve(User $user): bool {
        return $user->isAdmin();
    }
    public function getAllPendingProjects(User $user): bool {
        return $user->isAdmin();
    }
    public function updateProjectStatus(User $user): bool {
        return $user->isAdmin();
    }
    public function isAdmin(User $user): bool {
        return $user->isAdmin();
    }
}
