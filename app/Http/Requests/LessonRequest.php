<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'video_url' => 'nullable|url',
            'order' => 'nullable|integer|min:1',
           
        ];
    }
}
