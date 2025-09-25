<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'description'    => $this->description,
            'price'          => $this->price,
            'is_free'        => $this->is_free,
            'level'          => $this->level,
            'thumbnail_url'  => $this->thumbnail_url, // استعمل accessor
            'instructor'     => new UserResource($this->whenLoaded('instructor')),
            'category'       => new CategoryResource($this->whenLoaded('category')),
            'students_count' => $this->when(isset($this->students_count), $this->students_count),
            'lessons'        => LessonResource::collection($this->whenLoaded('lessons')),
            'assignments'    => AssignmentResource::collection($this->whenLoaded('assignments')),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
