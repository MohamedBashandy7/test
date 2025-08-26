<?php

namespace App\Http\Controllers\Verify;

use App\Http\Controllers\Controller\Controller;
use Illuminate\Http\Request;

class UsersVerifyController extends Controller {
    public static function register(Request $request) {
        if (!$request->has('email') || empty($request->email)) {
            return response([
                'success' => false,
                'message' => 'البريد الإلكتروني مطلوب',
            ], 400);
        }
        if (!$request->has('password') || empty($request->password)) {
            return response([
                'success' => false,
                'message' => 'كلمة المرور مطلوبة',
            ], 400);
        }
        if (!$request->has('name') || empty($request->name)) {
            return response([
                'success' => false,
                'message' => 'الاسم مطلوب',
            ], 400);
        }
        $allowedRoles = ['admin', 'project_manager', 'developer'];
        if (!$request->has('role') || empty($request->role)) {
            return response([
                'success' => false,
                'message' => 'النوع مطلوب',
            ], 400);
        }
        if (!in_array($request->role, $allowedRoles)) {
            return response([
                'success' => false,
                'message' => 'النوع يجب أن يكون واحد من: ' . implode('، ', $allowedRoles),
            ], 400);
        }
    }
    public static function addProjects(Request $request) {
        if (!$request->has('name') || empty($request->name)) {
            return response([
                'success' => false,
                'message' => 'اسم المشروع مطلوب',
            ], 400);
        }
        if (!$request->has('description') || empty($request->description)) {
            return response([
                'success' => false,
                'message' => 'الوصف مطلوب',
            ], 400);
        }
        $status = ['open', 'in_progress', 'completed'];
        if (!$request->has('status') || empty($request->status)) {
            return response([
                'success' => false,
                'message' => 'الحالة مطلوبة',
            ], 400);
        }
        if (!in_array($request->status, $status)) {
            return response([
                'success' => false,
                'message' => 'النوع يجب أن يكون واحد من: ' . implode('، ', $status),
            ], 400);
        }
        if (!$request->has('start_date') || empty($request->start_date)) {
            return response([
                'success' => false,
                'message' => 'تاريخ البدء مطلوب',
            ], 400);
        }
        if (!$request->has('end_date') || empty($request->end_date)) {
            return response([
                'success' => false,
                'message' => 'تاريخ الانتهاء مطلوب',
            ], 400);
        }
    }
    public static function addTasks(Request $request) {
        if (!$request->has('title') || empty($request->title)) {
            return response([
                'success' => false,
                'message' => 'العنوان مطلوب',
            ], 400);
        }
        if (!$request->has('description') || empty($request->description)) {
            return response([
                'success' => false,
                'message' => 'الوصف مطلوب',
            ], 400);
        }
        if (!$request->has('assigned_to') || empty($request->assigned_to)) {
            return response([
                'success' => false,
                'message' => 'النوع مطلوب',
            ], 400);
        }
        if (!$request->has('project_id') || empty($request->project_id)) {
            return response([
                'success' => false,
                'message' => 'النوع مطلوب',
            ], 400);
        }
        if (!$request->has('due_date') || empty($request->due_date)) {
            return response([
                'success' => false,
                'message' => 'النوع مطلوب',
            ], 400);
        }
    }
    public static function updateTaskByUser(Request $request) {
        if (!$request->has('status') || empty($request->status)) {
            return response([
                'success' => false,
                'message' => 'الحالة مطلوبة',
            ], 400);
        }
    }
    public static function updateProjectStatus(Request $request) {
        if (!$request->has('status') || empty($request->status)) {
            return response([
                'success' => false,
                'message' => 'الحالة مطلوبة',
            ], 400);
        }
    }
}
