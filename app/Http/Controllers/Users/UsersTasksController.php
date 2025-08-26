<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Tasks;
use App\Models\Projects;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Verify\MainVerifyController;


class UsersTasksController extends Controller {

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
        MainVerifyController::addTasks('addTasks', 'user')($request);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'project_id' => $request->project_id,
            'due_date' => $request->due_date,
            'updated_by' => Auth::id()
        ];

        $newAttachmentPath = $this->saveAttachmentFromRequest($request);
        if ($newAttachmentPath !== null) {
            if (!empty($task->attachment_path)) {
                $oldPath = public_path($task->attachment_path);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            $data['attachment_path'] = $newAttachmentPath;
        }

        $task->update($data);
        return response()->json($task);
    }



    private function saveAttachmentFromRequest(Request $request): ?string {
        $attachmentsDir = public_path('attachments');
        if (!File::exists($attachmentsDir)) {
            File::makeDirectory($attachmentsDir, 0775, true);
        }

        // Case 1: Traditional multipart file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid()->toString() . ($extension ? ('.' . $extension) : '');
            $file->move($attachmentsDir, $filename);
            return 'attachments/' . $filename;
        }

        // Case 2: JSON base64 payload under `attachment`
        $attachmentInput = $request->input('attachment');
        if (is_string($attachmentInput) && $attachmentInput !== '') {
            $base64Data = $attachmentInput;
            $extension = null;

            // data URL: data:mime/type;base64,xxxx
            if (preg_match('/^data:(.*?);base64,(.*)$/', $attachmentInput, $matches) === 1) {
                $mimeType = $matches[1] ?? null;
                $base64Data = $matches[2] ?? '';
                $extension = match ($mimeType) {
                    'application/pdf' => 'pdf',
                    'image/jpeg', 'image/jpg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'text/plain' => 'txt',
                    default => null
                };
            }

            // Fallback: try provided name or explicit extension fields
            if ($extension === null) {
                $providedName = (string) $request->input('attachment_name', '');
                if ($providedName !== '') {
                    $pathInfoExt = pathinfo($providedName, PATHINFO_EXTENSION);
                    $extension = $pathInfoExt !== '' ? $pathInfoExt : null;
                }
                if ($extension === null) {
                    $extension = (string) $request->input('attachment_extension', '') ?: null;
                }
            }

            $binary = base64_decode($base64Data, true);
            if ($binary === false) {
                return null;
            }

            $filename = Str::uuid()->toString() . ($extension ? ('.' . $extension) : '');
            File::put($attachmentsDir . DIRECTORY_SEPARATOR . $filename, $binary);
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

    public function updateStatus(Request $request, Tasks $task): JsonResponse {
        $this->authorize('updateStatus', $task);
        MainVerifyController::updateTaskByUser('updateTaskByUser', 'user')($request);
        $task->update(['status' => $request->status]);
        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully',
            'data' => $task
        ]);
    }
}
