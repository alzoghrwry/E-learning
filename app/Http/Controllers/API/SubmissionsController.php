<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SubmissionsController extends Controller
{
    /**
     * List submissions with filtering and pagination.
     * - Teachers: all submissions
     * - Students: only their own
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Submission::with(['student', 'assignment.course']);

        // إذا الطالب، عرض فقط تسليماته
        if ($user->role === 'student') {
            $query->where('student_id', $user->id);
        }

        // فلترة بالكورس
        if ($request->filled('course_id')) {
            $query->whereHas('assignment', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        // فلترة بالتكليف
        if ($request->filled('assignment_id')) {
            $query->where('assignment_id', $request->assignment_id);
        }

        // ترتيب حسب أحدث التسليمات أولاً
        $query->orderBy('created_at', 'desc');

        // استخدام pagination مع 10 لكل صفحة
        return SubmissionResource::collection($query->paginate(10));
    }

    /**
     * Store a submission (students only)
     */
   public function store(Request $request)
{
    $request->validate([
        'assignment_id' => 'required|exists:assignments,id',
        'file' => 'required|file|max:10240', // 🔴 الملف إلزامي
    ]);

    $user = Auth::user();
    if ($user->role !== 'student') {
        return response()->json(['message' => 'Only students can submit assignments.'], 403);
    }

    // رفع الملف وتخزينه داخل public disk
    $filePath = $request->file('file')->store('submissions', 'public');

    $submission = Submission::create([
        'assignment_id' => $request->assignment_id,
        'student_id' => $user->id,
        'file_path' => $filePath, // نخزن المسار فقط
    ]);

    return new SubmissionResource($submission->load(['student', 'assignment.course']));
}


    /**
     * Update submission (teachers only)
     */
    public function update(Request $request, Submission $submission)
    {
        $user = Auth::user();
        if ($user->role !== 'teacher') {
            return response()->json(['message' => 'Only teachers can review submissions.'], 403);
        }

        $request->validate([
            'grade' => 'nullable|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback,
        ]);

        return new SubmissionResource($submission->load(['student', 'assignment.course']));
    }

    /**
     * Delete submission
     * - Students can delete their own
     * - Teachers can delete any
     */
    public function destroy(Submission $submission)
    {
        $user = Auth::user();

        if ($user->role === 'student' && $submission->student_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($submission->file_path) {
            Storage::disk('public')->delete($submission->file_path);
        }

        $submission->delete();

        return response()->json(['message' => 'Submission deleted successfully.']);
    }
}
