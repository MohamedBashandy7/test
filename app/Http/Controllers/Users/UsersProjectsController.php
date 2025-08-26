<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\controller\Controller;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UsersProjectsController extends Controller {
    public function index(): JsonResponse {
        $user = Auth::user();
        $query = Projects::with('projectManager');

        if ($user->type !== 'admin') {
            $query->where('project_manager_id', $user->id);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    public function store(Request $request) {
        $this->authorize('create', Projects::class);
        // $user = Auth::user();
        // if (!Gate::allows('create', Projects::class)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Unauthorized. Only admins and project managers can create projects.'
        //     ], 403);
        // }

        // $validated = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'description' => 'nullable|string',
        //     'project_manager_id' => 'required|exists:users,id',
        //     'status' => 'required|in:open,in_progress,completed',
        //     'start_date' => 'required|date',
        //     'end_date' => 'nullable|date|after_or_equal:start_date'
        // ]);

        // // التحقق من صلاحية تعيين مدير المشروع
        // if (!Gate::allows('assignProjectManager', $user) && $validated['project_manager_id'] != $user->id) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'You can only assign yourself as project manager.'
        //     ], 403);
        // }

        // $project = Projects::create($validated);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Project created successfully',
        //     'data' => $project->load('projectManager')
        // ], 201);
    }

    public function show(Projects $project): JsonResponse {
        $user = Auth::user();

        // التحقق من الصلاحيات باستخدام Policy
        if (!Gate::allows('view', $project)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to view this project.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $project->load('projectManager')
        ]);
    }

    public function update(Request $request, Projects $project): JsonResponse {
        $user = Auth::user();

        // التحقق من الصلاحيات باستخدام Policy
        if (!Gate::allows('update', $project)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to update this project.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'project_manager_id' => 'sometimes|exists:users,id',
            'status' => 'sometimes|in:open,in_progress,completed',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        // التحقق من صلاحية تغيير مدير المشروع
        if (isset($validated['project_manager_id']) && !Gate::allows('assignProjectManager', $user)) {
            unset($validated['project_manager_id']);
        }

        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => $project->fresh('projectManager')
        ]);
    }

    public function destroy(Projects $project): JsonResponse {
        $user = Auth::user();

        // التحقق من الصلاحيات باستخدام Policy
        if (!Gate::allows('delete', $project)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to delete this project.'
            ], 403);
        }

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully'
        ]);
    }
}
