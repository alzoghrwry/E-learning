<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student' => [
                'id' => $this->student->id,
                'name' => $this->student->name,
            ],
            'assignment' => [
                'id' => $this->assignment->id,
                'title' => $this->assignment->title,
                'course' => [
                    'id' => $this->assignment->course->id,
                    'title' => $this->assignment->course->title,
                ],
            ],
            'file_path' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'grade' => $this->grade,
            'feedback' => $this->feedback,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
