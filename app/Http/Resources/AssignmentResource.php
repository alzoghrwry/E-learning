<?php

namespace App\Http\Resources;
 use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
  

public function toArray($request)
{
    return [
        'id' => $this->id,
        'course_id' => $this->course_id,
        'title' => $this->title,
        'description' => $this->description,
        'due_date' => $this->due_date ? Carbon::parse($this->due_date)->format('Y-m-d H:i:s') : null,
        'file_url' => $this->file_url,
        'course' => $this->whenLoaded('course') ? new CourseResource($this->course) : null,
        'submissions' => $this->whenLoaded('submissions') ? SubmissionResource::collection($this->submissions) : [],
        'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('Y-m-d H:i:s') : null,
        'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('Y-m-d H:i:s') : null,
    ];
}

}
