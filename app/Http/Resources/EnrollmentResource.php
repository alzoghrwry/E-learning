<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'student' => new UserResource($this->whenLoaded('student')),
            'course' => new CourseResource($this->whenLoaded('course')),
            'enrolled_at' => $this->enrolled_at ,
    
        ];
    }
}
