<?php

namespace App\Http\Controllers\Verify;

use App\Http\Controllers\Controller\Controller;
use Illuminate\Http\Request;

class SharedVerifyController extends Controller {
    public static function verify(Request $request) {
        if (!$request->has('email') || empty($request->email)) {
            return response([
                'success' => false,
                'message' => 'البريد الإلكتروني مطلوب',
            ], 400);
        }
        if (!$request->has('otp') || empty($request->otp)) {
            return response([
                'success' => false,
                'message' => 'الرمز مطلوب',
            ], 400);
        }
    }
    public static function login(Request $request) {
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
    }
}
