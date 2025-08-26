<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Tasks;
use App\Models\Projects;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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
        $attachmentPath = $this->saveAttachmentFromRequest($request);
        $task = Tasks::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'to_do',
            'assigned_to' => $request->assigned_to,
            'project_id' => $request->project_id,
            'due_date' => $request->due_date,
            'attachment_path' => $attachmentPath
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task->load(['assignee', 'project'])
        ]);
    }

    public function update(Request $request, Tasks $task): JsonResponse {
        $this->authorize('update', $task);
        $task->update(['status' => $request->status]);
        return response()->json($task);
    }

    private function saveAttachmentFromRequest(Request $request): ?string {
        $attachmentsDir = public_path('attachments');
        if (!File::exists($attachmentsDir)) {
            File::makeDirectory($attachmentsDir, 0775, true);
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid()->toString() . ($extension ? ('.' . $extension) : '');
            $file->move($attachmentsDir, $filename);
            return 'attachments/' . $filename;
        }

        return null;
    }

    public function destroy(Tasks $task) {
        $this->authorize('delete', $task);
        $task->forceDelete();
        $message = 'Task permanently deleted successfully';
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

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