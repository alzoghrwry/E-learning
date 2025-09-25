<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class StudentController extends Controller
{
    /**
     * تسجيل حساب جديد للطلاب
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $student = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
        ]);

        $token = $student->createToken('student-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($student),
            'token' => $token
        ], 201);
    }

    /**
     * تسجيل دخول الطالب
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $student = User::where('email', $request->email)->where('role', 'student')->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        $token = $student->createToken('student-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($student),
            'token' => $token
        ]);
    }

    /**
     * عرض بيانات الطالب الحالي
     */
    public function profile(Request $request)
    {
        $student = $request->user();

        if ($student->role !== 'student') {
            return response()->json(['message' => 'ليس لديك صلاحية'], 403);
        }

        return new UserResource($student);
    }
}
