<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\controller\Controller ;
use App\Models\User;
use App\Models\Verify;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Verify\MainVerifyController;

class UsersController extends Controller {
    // public function login(Request $request) {
    //     $verify = UsersVerifyController::login($request);
    //     if ($verify) {
    //         return $verify;
    //     }
    //     $checkIfUserExistsVerify = $this->checkIfUserExists($request->email, '1', $request->type);
    //     $checkIfUserExistsNotVerify = $this->checkIfUserExists($request->email, '0', $request->type);
    //     if ($checkIfUserExistsNotVerify) {
    //         return response([
    //             'success' => false,     
    //             'message' => 'go_to_verify',
    //         ]);
    //     }
    //     if ($checkIfUserExistsVerify) {
    //         $user = User::where('email', $request->email)->where('type', $request->type)->first() ?? NULL;
    //         if (!Hash::check($request->password, $user->password)) {
    //             return response([
    //                 'success' => false,
    //                 'message' => 'كلمة المرور غير صحيحة',
    //             ], 401);
    //         }
    //         $token = $user->createToken('MO->' . $user->email)->plainTextToken;
    //         $user['token'] = $token;
    //         $user['success'] = true;
    //         return $user;
    //     } else {
    //         return response([
    //             'success' => false,
    //             'message' => 'رقم الجوال غير موجود',
    //         ], 401);
    //     }
    // }
    public function register(Request $request) {
        // First validate the request
        MainVerifyController::register('register', 'user')($request);
        
        $existingUserWithVerify = $this->checkIfUserExists($request->email, '1', $request->role);
        $existingUserWithNotVerify = $this->checkIfUserExists($request->email, '0', $request->role);

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
                'type' => $request->type,
                'is_verify' => '0',
                'password' => Hash::make($request->password),
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'type' => $request->type,
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
    public function checkIfUserExists($email, $is_verify, $role) {
        $existingUser = User::where('email', $email)
            ->where('is_verify', $is_verify)
            ->where('role', $role)
            ->first();  
        return $existingUser;
    }
    public function VerifyAccount(Request $request) {
        // $verify = AppUserVerifyController::VerifyAccount($request);
        // if ($verify) {
        //     return $verify;
        // }

        // $verification = Verify::where('email', $request->email)
        //     ->where('code', $request->otp)
        //     ->first();

        $cacheKey = 'otp_attempts_' . $request->email;
        $blockKey = 'otp_blocked_' . $request->email;
        $blockExpiryKey = 'otp_blocked_' . $request->email . '_expiry';
        if (cache()->has($blockKey)) {
            $blockExpiry = cache()->get($blockExpiryKey);

            if (now()->greaterThanOrEqualTo($blockExpiry)) {
                cache()->forget($blockKey);
                cache()->forget($blockExpiryKey);
                cache()->put($cacheKey, 0, now()->addMinutes(5)); // إعادة تهيئة المحاولات لصفر بعد الحظر
            } else {
                $remainingMinutes = now()->diffInMinutes($blockExpiry);
                return response()->json([
                    'success' => false,
                    'message' => 'تم تعطيل إدخال الرمز مؤقتًا. يرجى المحاولة بعد ' . $remainingMinutes . ' دقيقة',
                    'remaining_attempts' => 0
                ], 429);
            }
        }

        // if (!$verification) {
        //     if (!cache()->has($cacheKey)) {
        //         $failedAttempts = 1;
        //         cache()->put($cacheKey, $failedAttempts, now()->addMinutes(5));
        //     } else {
        //         $failedAttempts = cache()->increment($cacheKey);
        //     }

        //     if ($failedAttempts >= 5) {
        //         Verify::where('email', $request->email)->delete();
        //         $blockExpiry = now()->addMinutes(5);
        //         cache()->put($blockKey, true, $blockExpiry);
        //         cache()->put($blockExpiryKey, $blockExpiry, $blockExpiry);

        //         return response()->json([
        //             'success' => false,
        //             'message' => 'لقد تجاوزت الحد المسموح من المحاولات. يرجى المحاولة بعد 5 دقيقة',
        //             'remaining_attempts' => 0
        //         ], 429);
        //     }

        //     $remainingAttempts = 5 - $failedAttempts;
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'رمز التحقق غير صحيح. لديك ' . $remainingAttempts . ' محاولات متبقية',
        //         'remaining_attempts' => $remainingAttempts,
        //     ], 400);
        // }

        // if ($verification->created_at->diffInMinutes(now()) > 5) {
        //     $verification->delete();
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'انتهت صلاحية رمز التحقق. يرجى طلب رمز جديد',
        //     ], 400);
        // }

        $user = User::where('email', $request->email)->where('type', $request->type);
        $user->update([
            'is_verify' => '1',
        ]);

        // $verification->delete();
        // cache()->forget($cacheKey);
        // cache()->forget($blockKey);
        // cache()->forget($blockExpiryKey);

        return response()->json([
            'success' => true,
            'message' => 'تم التحقق بنجاح',
        ]);
    }
    public function update(Request $request, User $user) {
        // $verify = AppUserVerifyController::updateUser($request);
        // if ($verify) {
        //     return $verify;
        // }
        $authenticatedUser = $request->user();
        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'national_identity' => $request->national_identity,
            'place_id' => $request->place_id,
        ];

        // Handle base64 image if provided
        if ($request->filled('img') && is_string($request->img)) {
            $destinationPath = public_path('Users');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // Delete old image if exists
            if (!empty($authenticatedUser->img) && file_exists(public_path($authenticatedUser->img))) {
                unlink(public_path($authenticatedUser->img));
            }

            // Process base64 image
            $image_parts = explode(";base64,", $request->img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1] ?? 'png';
            $image_base64 = base64_decode($image_parts[1]);
            $imageName = time() . '_' . uniqid() . '.' . $image_type;
            $file = $destinationPath . '/' . $imageName;
            file_put_contents($file, $image_base64);
            $updateData['img'] = 'Users/' . $imageName;
        }

        $authenticatedUser->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث البيانات بنجاح',
            'user' => $authenticatedUser->fresh()
        ]);
    }
    public function UpdatePassword(Request $request) {
        // $verify = AppUserVerifyController::UpdatePassword($request);
        // if ($verify) {
        //     return $verify;
        // }
        $authenticatedUser = $this->checkIfUserExists($request->email, '1', $request->type);
        if ($authenticatedUser) {
            if ($request->filled('old_password') || $request->filled('new_password')) {
                if (!Hash::check($request->old_password, $authenticatedUser->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'كلمة المرور الحالية غير صحيحة',
                    ], 400);
                }
                $authenticatedUser->update([
                    'password' => Hash::make($request->new_password)
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث كلمة المرور بنجاح',
                'user' => $authenticatedUser->fresh()
            ]);
        } else {
        }
    }
    public function searchCaptain(Request $request) {
        $email = $request->input('email');
        
        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الجوال مطلوب'
            ], 400);
        }
        
        $captains = User::where('type', 'captain')
                      ->where('is_verify', '1')
                      ->where('email', 'like', '%' . $email . '%')
                      ->get();
        
        return response()->json([
            'success' => true,
            'captains' => $captains
        ]);
    }
}
