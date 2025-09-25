<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentRequest;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = Assignment::with(['course', 'submissions'])->get();
        return AssignmentResource::collection($assignments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssignmentRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file_url'] = $request->file('file')->store('assignments', 'public');
        }

        $assignment = Assignment::create($data);

        return new AssignmentResource($assignment->load(['course', 'submissions']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        return new AssignmentResource($assignment->load(['course', 'submissions']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssignmentRequest $request, Assignment $assignment)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            // حذف الملف القديم إذا موجود
            if ($assignment->file_url) {
                Storage::disk('public')->delete($assignment->file_url);
            }
            $data['file_url'] = $request->file('file')->store('assignments', 'public');
        }

        $assignment->update($data);

        return new AssignmentResource($assignment->load(['course', 'submissions']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        if ($assignment->file_url) {
            Storage::disk('public')->delete($assignment->file_url);
        }

        $assignment->delete();

        return response()->json([
            'message' => 'Assignment deleted successfully.'
        ]);
    }
}
