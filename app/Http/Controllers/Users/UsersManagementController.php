<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\controller\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UsersManagementController extends Controller {
    public function index(): JsonResponse {
        // التحقق من الصلاحيات باستخدام Policy
        if (!Gate::allows('viewAny', User::class)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to view users.'
            ], 403);
        }

        $users = User::all();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request): JsonResponse {
        // التحقق من الصلاحيات باستخدام Policy
        if (!Gate::allows('create', User::class)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to create users.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'type' => 'required|in:admin,project_manager,developer'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type' => $validated['type'],
            'is_verify' => '1'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    public function show(User $user): JsonResponse {
        // التحقق من الصلاحيات باستخدام Policy
        if (!Gate::allows('view', $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to view this user.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, User $user): JsonResponse {
        // التحقق من الصلاحيات باستخدام Policy
        if (!Gate::allows('update', $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to update this user.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'type' => 'sometimes|in:admin,project_manager,developer'
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    public function destroy(User $user): JsonResponse {
        // التحقق من الصلاحيات باستخدام Policy
        if (!Gate::allows('delete', $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to delete this user.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
