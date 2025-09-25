<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'course_id'   => $this->course_id,
            'course'      => new CourseResource($this->whenLoaded('course')), // ✅
            'title'       => $this->title,
            'content'     => $this->content,
            'video_full_url' => $this->video_full_url,
            'order'       => $this->order,
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
