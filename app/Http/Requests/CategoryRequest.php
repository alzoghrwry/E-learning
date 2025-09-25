<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        
        return auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        $categoryId = $this->route('category'); 

        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'description' => 'nullable|string|max:1000',
            'thumbnail' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفئة مطلوب.',
            'name.unique' => 'هذا الاسم مستخدم مسبقًا.',
            'description.max' => 'الوصف طويل جدًا.',
            'thumbnail.image' => 'الصورة يجب أن تكون ملف صورة صالح.',
            'thumbnail.mimes' => 'الصورة يجب أن تكون بصيغة jpg, jpeg, أو png.',
            'thumbnail.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.',
        ];
    }
}
