<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    // تسجيل مستخدم جديد
    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();
        $profilePhotoUrl = $request->profile_photo ?? null;

        $role = 'student';
        if ($request->user() && $request->user()->role === 'admin') {
            $role = $data['role'] ?? 'student';
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'is_active' => true,
            'profile_photo' => $profilePhotoUrl,
        ]);

        // إنشاء توكن مباشرة بعد التسجيل
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم إنشاء المستخدم بنجاح',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201); 
    }

    // تسجيل الدخول
    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user = User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة',
                'errors' => ['email' => ['البريد أو كلمة المرور غير صحيحة']]
            ], 401);
        }

        if(!$user->is_active){
            return response()->json([
                'message' => 'الحساب غير مفعل',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user // ← إضافة بيانات المستخدم هنا
        ], 200);
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ], 200);
    }

    // طلب رابط إعادة تعيين كلمة المرور
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني',
                'status_code' => 200
            ]);
        } else {
            return response()->json([
                'message' => 'البريد الإلكتروني غير موجود',
                'status_code' => 404
            ]);
        }
    }

    // إعادة تعيين كلمة المرور
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'تم إعادة تعيين كلمة المرور بنجاح',
                'status_code' => 200
            ]);
        } else {
            return response()->json([
                'message' => 'الرمز غير صالح أو البريد الإلكتروني غير موجود',
                'status_code' => 400
            ]);
        }
    }

    // جلب بيانات المستخدم الحالي
    public function me(Request $request)
    {
        return response()->json($request->user(), 200);
    }
}
