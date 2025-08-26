<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\controller\Controller;
use App\Http\Controllers\Verify\MainVerifyController;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


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

    public function update(Request $request, Projects $project): JsonResponse {
        $this->authorize('update', Projects::class);
        MainVerifyController::addProjects('addProjects', 'user')($request);
        $project->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => $project->fresh('projectManager')
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
}