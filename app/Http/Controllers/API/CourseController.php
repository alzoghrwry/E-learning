<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    // عرض جميع الكورسات
    public function index()
    {
        $courses = Course::with('category')->get();

        return response()->json([
            'message' => 'Courses retrieved successfully',
            'status_code' => 200,
            'data' => CourseResource::collection($courses)
        ]);
    }

    // إنشاء كورس جديد
    public function store(CourseRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = asset('storage/' . $path);
        }

        $course = Course::create($data);

        return response()->json([
            'message' => 'Course created successfully',
            'status_code' => 201,
            'data' => new CourseResource($course)
        ], 201);
    }

    // عرض كورس واحد
    public function show(Course $course)
    {
        return response()->json([
            'message' => 'Course retrieved successfully',
            'status_code' => 200,
            'data' => new CourseResource($course->load('category'))
        ]);
    }

    // تحديث كورس
    public function update(CourseRequest $request, Course $course)
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            // حذف الصورة القديمة إن وجدت
            if ($course->thumbnail) {
                $oldPath = str_replace(asset('storage/'), '', $course->thumbnail);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = asset('storage/' . $path);
        }

        $course->update($data);

        return response()->json([
            'message' => 'Course updated successfully',
            'status_code' => 200,
            'data' => new CourseResource($course)
        ]);
    }

public function publicCourses()
{
    try {
        // جلب كل الكورسات مع الفئة المرتبطة
        $courses = Course::with('category')->get();

        return response()->json([
            'message' => 'Courses retrieved successfully',
            'status_code' => 200,
            'data' => CourseResource::collection($courses)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error fetching courses',
            'status_code' => 500,
            'error' => $e->getMessage()
        ], 500);
    }
}



    // حذف كورس
    public function destroy(Course $course)
    {
        if ($course->thumbnail) {
            $oldPath = str_replace(asset('storage/'), '', $course->thumbnail);
            Storage::disk('public')->delete($oldPath);
        }

        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully',
            'status_code' => 200
        ]);
    }
}
