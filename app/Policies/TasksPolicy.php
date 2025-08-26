<?php

namespace App\Policies;

use App\Models\Tasks;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TasksPolicy {
    public function viewAny(User $user): bool {
        return $user->isAdmin() || $user->isProjectManager();
    }

    public function view(User $user, Tasks $task): bool {
        // Users can view tasks assigned to them or tasks in projects they manage
        return $user->id === $task->assigned_to ||
            $user->id === $task->project->project_manager_id ||
            $user->isAdmin();
    }

    public function create(User $user): bool {
        return $user->isAdmin() || $user->isProjectManager();
    }

    public function update(User $user, Tasks $task): bool {
        return $user->id === $task->assigned_to && $user->isDeveloper();
    }

    public function updateStatus(User $user, Tasks $task): bool {
        // Only the assigned user can update the task status
        return $user->id === $task->assigned_to;
    }

    public function delete(User $user, Tasks $task): bool {
        // Only admins and project managers can delete tasks
        return $user->isAdmin() || $user->id === $task->project->project_manager_id;
    }

    public function restore(User $user, Tasks $task): bool {
        return $user->isAdmin() || $user->isProjectManager();
    }

    public function forceDelete(User $user, Tasks $task): bool {
        return $user->isAdmin();
    }
}