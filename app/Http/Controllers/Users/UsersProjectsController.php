<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\controller\Controller;
use App\Http\Controllers\Verify\MainVerifyController;
use App\Models\Projects;
use App\Events\ProjectApprovalRequested;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Events\ProjectApproved;
use App\Events\ProjectRejected;


class UsersProjectsController extends Controller {
    // public function index(): JsonResponse {
    //     $user = Auth::user();
    //     $query = Projects::with('projectManager');

    //     if ($user->type !== 'admin') {
    //         $query->where('project_manager_id', $user->id);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => $query->get()
    //     ]);
    // }

    public function store(Request $request) {
        $this->authorize('create', Projects::class);
        MainVerifyController::addProjects('addProjects', 'user')($request);
        $projectData = $request->all();
        $projectData['project_manager_id'] = Auth::id();
        $project = Projects::create($projectData);
        event(new ProjectApprovalRequested($project, 'created'));
        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data' => $project->load('projectManager')
        ]);
    }

    // public function show(Projects $project): JsonResponse {

    //     return response()->json([
    //         'success' => true,
    //         'data' => $project->load('projectManager')
    //     ]);
    // }



    public function getAllPendingProjects(): JsonResponse {
        $this->authorize('getAllPendingProjects', Projects::class);
        $projects = Projects::where('approval_status', 'pending')
            ->with('projectManager')
            ->latest()
            ->get();
        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }
    public function destroy(Projects $project) {
        $this->authorize('delete', Projects::class);
        $project->forceDelete(); // This will permanently delete the record
        return response()->json([
            'success' => true,
            'message' => 'Project permanently deleted successfully'
        ]);
    }
    public function getAllProjects(Request $request): JsonResponse {
        $searchQuery = (string) $request->query('q', '');
        $projectsQuery = Projects::with('projectManager')
            ->when($searchQuery !== '', function ($query) use ($searchQuery) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'like', "%{$searchQuery}%");
                });
            });
        $projects = $projectsQuery->get();
        return response()->json([
            'success' => true,
            'data' => $projects,
        ]);
    }
    public function update(Request $request, Projects $project): JsonResponse {
        $this->authorize('update', Projects::class);
        MainVerifyController::addProjects('addProjects', 'user')($request);
        $project->update($request->all());
        event(new ProjectApprovalRequested($project, 'updated'));
        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => $project->fresh('projectManager')
        ]);
    }
    public function updateStatus(Request $request, Projects $project): JsonResponse {
        $this->authorize('approve', Projects::class);
        MainVerifyController::updateProjectStatus('updateProjectStatus', 'user')($request);
        $status = $request->status;

        if (!in_array($status, ['approve', 'reject'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status. Use approve or reject.'
            ], 422);
        }

        $update = [
            'approval_status' => $status . 'd',
            // Clear the opposite status fields
            'approved_by' => $status === 'approve' ? Auth::id() : null,
            'approved_at' => $status === 'approve' ? now() : null,
            'rejected_by' => $status === 'reject' ? Auth::id() : null,
            'rejected_at' => $status === 'reject' ? now() : null
        ];

        $project->update($update);

        if ($status === 'approve') {
            event(new ProjectApproved($project));
        } else {
            event(new ProjectRejected($project));
        }

        return response()->json([
            'success' => true,
            'message' => "Project {$status}d successfully",
            'data' => $project->fresh(['projectManager', 'approver', 'rejecter'])
        ]);
    }
}
