<?php

namespace App\Policies;

use App\Models\Projects;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectsPolicy {
    public function viewAny(User $user): bool {
        return true; // يمكن للجميع رؤية قائمة المشاريع
    }

    public function view(User $user, Projects $projects): bool {
        // يمكن للمدير أو مدير المشروع المخصص رؤية المشروع
        return $user->isAdmin() || $projects->project_manager_id === $user->id;
    }

    public function create(User $user): bool {
        return $user->isAdmin() || $user->isProjectManager();
    }

    public function update(User $user, Projects $projects): bool {
        // يمكن للمدير أو مدير المشروع المخصص تحديث المشروع
        return $user->isAdmin() || $projects->project_manager_id === $user->id;
    }

    public function delete(User $user, Projects $projects): bool {
        // يمكن للمدير أو مدير المشروع المخصص حذف المشروع
        return $user->isAdmin() || $projects->project_manager_id === $user->id;
    }

    public function assignProjectManager(User $user): bool {
        // فقط المدير يمكنه تعيين مديري مشاريع
        return $user->isAdmin();
    }
}