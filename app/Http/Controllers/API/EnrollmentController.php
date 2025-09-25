<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Course;
use App\Http\Resources\EnrollmentResource;

class EnrollmentController extends Controller
{
    // عرض كل التسجيلات (للمدير فقط أو الطالب لتسجيلاته)
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $enrollments = Enrollment::with(['student', 'course'])->get();
        } else {
            $enrollments = Enrollment::with(['student', 'course'])
                                     ->where('student_id', $user->id)
                                     ->get();
        }

        return EnrollmentResource::collection($enrollments);
    }

    // تسجيل طالب في كورس
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = $request->user();

        // السماح فقط للطالب بالتسجيل
        if ($user->role !== 'student') {
            return response()->json([
                'message' => 'فقط الطلاب يمكنهم التسجيل في الكورسات'
            ], 403);
        }

        // تحقق من أن الطالب لم يسجل مسبقًا في نفس الكورس
        $exists = Enrollment::where('student_id', $user->id)
                            ->where('course_id', $request->course_id)
                            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'أنت مسجل بالفعل في هذا الكورس'
            ], 400);
        }

        $enrollment = Enrollment::create([
            'student_id'  => $user->id,
            'course_id'   => $request->course_id,
            'enrolled_at' => now(),
        ]);

        return response()->json([
            'message'    => 'تم التسجيل بنجاح',
            'enrollment' => new EnrollmentResource($enrollment->load(['student', 'course']))
        ], 201);
    }

    // عرض تسجيل محدد
    public function show(Request $request, $id)
    {
        $enrollment = Enrollment::with(['student', 'course'])->findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'admin' && $user->id !== $enrollment->student_id) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        return new EnrollmentResource($enrollment);
    }

    // حذف تسجيل
    public function destroy(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'admin' && $user->id !== $enrollment->student_id) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $enrollment->delete();

        return response()->json(['message' => 'تم حذف التسجيل بنجاح'], 200);
    }
}
