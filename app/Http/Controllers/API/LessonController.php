<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
   public function index(Request $request)
{
    $query = Lesson::with('course');

    // فلترة بالبحث (العنوان أو المحتوى)
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('content', 'like', '%' . $request->search . '%');
        });
    }

    // فلترة بالكورس
    if ($request->filled('course_id')) {
        $query->where('course_id', $request->course_id);
    }

    // ترتيب (اختياري)
    $query->orderBy('order', 'asc');

    // رجع البيانات مع الكورس
    return LessonResource::collection($query->paginate(10)); // ✅ مع pagination
}

 public function store(LessonRequest $request)
{
    $data = $request->validated();

    // رفع الملف إذا تم تحميله
    if ($request->hasFile('file')) {
        $data['resource_url'] = $request->file('file')->store('lessons/resources', 'public');
    }

    $lesson = Lesson::create($data);

    return new LessonResource($lesson->load('course'));
}

public function update(LessonRequest $request, Lesson $lesson)
{
    $data = $request->validated();

    if ($request->hasFile('file')) {
        $data['resource_url'] = $request->file('file')->store('lessons/resources', 'public');
    }

    $lesson->update($data);

    return new LessonResource($lesson->load('course'));
}





    public function show(Lesson $lesson)
    {
        return new LessonResource($lesson->load('course'));
    }

    // public function update(LessonRequest $request, Lesson $lesson)
    // {
    //     $data = $request->validated();

    //     if ($request->hasFile('resource')) {
    //         $data['resource'] = $request->file('resource')->store('lessons/resources', 'public');
    //     }

    //     $lesson->update($data);

    //     return new LessonResource($lesson);
    // }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return response()->noContent();
    }
}
