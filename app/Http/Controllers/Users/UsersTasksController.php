<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Tasks;
use App\Models\Projects;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Verify\MainVerifyController;

class UsersTasksController extends Controller {
    public function index() {
        // $user = Auth::user();
        // $query = Tasks::with(['assignee', 'project']);

        // if (!$user->isAdmin()) {
        //     $query->where(function($q) use ($user) {
        //         $q->where('assigned_to', $user->id)
        //           ->orWhereHas('project', function($project) use ($user) {
        //               $project->where('project_manager_id', $user->id);
        //           });
        //     });
        // }

        // return response()->json([
        //     'success' => true,
        //     'data' => $query->get()
        // ]);
    }

    public function store(Request $request): JsonResponse {
        $this->authorize('create', Tasks::class);
        MainVerifyController::addTasks('addTasks', 'user')($request);
        $task = Tasks::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'to_do',
            'assigned_to' => $request->assigned_to,
            'project_id' => $request->project_id,
            'due_date' => $request->due_date,
            'attachment_path' => $request->file('attachment') ?
                $request->file('attachment')->store('attachments') : null
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task->load(['assignee', 'project'])
        ]);
    }

    public function show(Tasks $task): JsonResponse {
        $this->authorize('view', $task);
        return response()->json([
            'success' => true,
            'data' => $task->load(['assignee', 'project'])
        ]);
    }

    public function update(Request $request, Tasks $task): JsonResponse {
        $this->authorize('update', $task);

        $updatable = [
            'title',
            'description',
            'assigned_to',
            'due_date',
            'attachment_path'
        ];

        $data = $request->only($updatable);

        if ($request->has('status')) {
            $this->authorize('updateStatus', $task);
            $data['status'] = $request->status;
        }

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('attachments');
        }

        $task->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $task->fresh(['assignee', 'project'])
        ]);
    }

    public function destroy(Tasks $task) {
        // $this->authorize('delete', $task);

        // if ($task->trashed()) {
        //     $task->forceDelete();
        //     $message = 'Task permanently deleted successfully';
        // } else {
        //     $task->delete();
        //     $message = 'Task moved to trash successfully';
        // }

        // return response()->json([
        //     'success' => true,
        //     'message' => $message
        // ]);
    }

    /**
     * Get all tasks for a specific project
     */
    public function getProjectTasks(Projects $project) {
        // $user = Auth::user();

        // // Check if user has access to this project
        // if (!$user->isAdmin() && $user->id !== $project->project_manager_id) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Unauthorized to view tasks for this project'
        //     ], 403);
        // }

        // $tasks = $project->tasks()
        //     ->with(['assignee', 'project'])
        //     ->when(!$user->isAdmin() && !$user->isProjectManager(), function($query) use ($user) {
        //         // Regular users can only see their own tasks
        //         return $query->where('assigned_to', $user->id);
        //     })
        //     ->get();

        // return response()->json([
        //     'success' => true,
        //     'data' => $tasks
        // ]);
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, Tasks $task): JsonResponse {
        $this->authorize('updateStatus', $task);

        $validated = $request->validate([
            'status' => 'required|in:to_do,in_progress,done'
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully',
            'data' => $task->fresh(['assignee', 'project'])
        ]);
    }
}
