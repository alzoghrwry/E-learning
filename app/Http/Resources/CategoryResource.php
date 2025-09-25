<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'description'=>$this->description,
            'thumbnail_url'=>$this->thumbnail_url, // accessor
            'courses'=>CourseResource::collection($this->whenLoaded('courses')),
            'created_at'=>$this->created_at->format('Y-m-d H:i:s'),
            'updated_at'=>$this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
