<?php

namespace App\Http\Controllers\Verify;

use App\Http\Controllers\Controller\Controller;
use Illuminate\Http\Request;

class UsersVerifyController extends Controller
{
    public static function register(Request $request) {
        if(!$request->has('email') || empty($request->email)){
            return response([
                'success' => false,
                'message' => 'البريد الإلكتروني مطلوب',
            ], 400);
        }
        if(!$request->has('password') || empty($request->password)){
            return response([
                'success' => false,
                'message' => 'كلمة المرور مطلوبة',
            ], 400);
        }
        if(!$request->has('name') || empty($request->name)){
            return response([
                'success' => false,
                'message' => 'الاسم مطلوب',
            ], 400);
        }
        $allowedRoles = ['admin', 'project_manager', 'developer'];
        if(!$request->has('role') || empty($request->role)){
            return response([
                'success' => false,
                'message' => 'النوع مطلوب',
            ], 400);
        }
        if(!in_array($request->role, $allowedRoles)){
            return response([
                'success' => false,
                'message' => 'النوع يجب أن يكون واحد من: ' . implode('، ', $allowedRoles),
            ], 400);
        }
    }
}
