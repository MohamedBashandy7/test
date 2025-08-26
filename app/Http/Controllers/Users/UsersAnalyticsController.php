<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Projects;
use App\Models\Tasks;
use Illuminate\Http\JsonResponse;

class UsersAnalyticsController extends Controller {

    public function stats(): JsonResponse {
        $this->authorize('isAdmin', Projects::class);
        $projectsByStatus = Projects::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $completedTasksPerUser = Tasks::selectRaw('assigned_to, COUNT(*) as total')
            ->where('status', 'done')
            ->groupBy('assigned_to')
            ->with('assignee:id,name')
            ->get()
            ->map(function ($row) {
                return [
                    'user_id' => $row->assigned_to,
                    'user_name' => $row->assignee->name ?? null,
                    'completed_tasks' => $row->total,
                ];
            });

        $mostActiveUsers = Tasks::selectRaw('updated_by, COUNT(*) as updates_count')
            ->whereNotNull('updated_by')
            ->groupBy('updated_by')
            ->orderByDesc('updates_count')
            ->with('updater:id,name')
            ->take(5)
            ->get()
            ->map(function ($row) {
                return [
                    'user_id' => $row->updated_by,
                    'user_name' => $row->updater->name ?? null,
                    'updates_count' => $row->updates_count,
                ];
            });

        return response()->json([
            'projects_by_status' => $projectsByStatus,
            'completed_tasks_per_user' => $completedTasksPerUser,
            'most_active_users' => $mostActiveUsers,
        ]);
    }
}
