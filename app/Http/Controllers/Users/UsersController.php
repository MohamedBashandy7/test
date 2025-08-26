<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\controller\Controller;
use App\Models\User;
use App\Models\Verify;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Verify\MainVerifyController;

class UsersController extends Controller {
    public function login(Request $request) {
        MainVerifyController::login('login', 'shared')($request);
        $checkIfUserExistsVerify = $this->checkIfUserExists($request->email, '1', $request->type);
        $checkIfUserExistsNotVerify = $this->checkIfUserExists($request->email, '0', $request->type);
        if ($checkIfUserExistsNotVerify) {
            $verificationCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            Verify::updateOrCreate(
                ['email' => $request->email],
                ['code' => $verificationCode]
            );
            Mail::to($request->email)->send(new VerificationCodeMail($verificationCode, $request->email));

            return response([
                'success' => false,
                'message' => 'تم إرسال كود التحقق إلى بريدك الإلكتروني',
                'error_type' => 'verify'
            ]);
        }
        if ($checkIfUserExistsVerify) {
            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password)) {
                return response([
                    'success' => false,
                    'message' => 'كلمة المرور غير صحيحة',
                ], 401);
            }
            $token = $user->createToken('MO->' . $user->email)->plainTextToken;
            $user['token'] = $token;
            $user['success'] = true;
            return $user;
        } else {
            return response([
                'success' => false,
                'message' => 'البريد الإلكتروني غير موجود',
            ], 401);
        }
    }
    public function register(Request $request) {
        // First validate the request
        MainVerifyController::register('register', 'user')($request);

        $existingUserWithVerify = $this->checkIfUserExists($request->email, '1');
        $existingUserWithNotVerify = $this->checkIfUserExists($request->email, '0');

        if ($existingUserWithVerify) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني موجود بالفعل',
            ], 400);
        }
        $verificationCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        if ($existingUserWithNotVerify) {
            // Update existing unverified user
            $user = $existingUserWithNotVerify;
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'is_verify' => '0',
                'password' => Hash::make($request->password),
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'is_verify' => '0',
                'password' => Hash::make($request->password),
            ]);
        }
        Verify::updateOrCreate(
            ['email' => $user->email],
            ['code' => $verificationCode]
        );
        Mail::to($request->email)->send(new VerificationCodeMail($verificationCode, $request->email));
        return response()->json([
            'success' => true,
            'message' => 'تم إرسال كود التفعيل إلى بريدك الإلكتروني',
            'user' => $user
        ]);
    }
    public function checkIfUserExists($email, $is_verify) {
        $existingUser = User::where('email', $email)
            ->where('is_verify', $is_verify)
            ->first();
        return $existingUser;
    }
    public function VerifyAccount(Request $request) {
        MainVerifyController::verify('verify', 'shared')($request);
        $verification = Verify::where('email', $request->email)
            ->where('code', $request->otp)
            ->first();
        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'الكود غير صحيح',
            ], 400);
        }
        $user = User::where('email', $request->email)->first();
        $user->update([
            'is_verify' => '1',
        ]);
        Verify::where('email', $request->email)->delete();
        return response()->json([
            'success' => true,
            'message' => 'تم التحقق بنجاح',
        ]);
    }
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }
}
