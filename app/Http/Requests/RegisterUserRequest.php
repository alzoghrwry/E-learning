<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'role'          => 'required|in:student,teacher,admin',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'profile_photo' => 'nullable|string|max:255',

        ];
    }

    public function messages(): array
    {
        return [
            'profile_photo.image' => 'حقل الصورة يجب أن يكون صورة.',
            'profile_photo.mimes' => 'يجب أن تكون الصورة من نوع jpg أو jpeg أو png.',
        ];
    }
}
