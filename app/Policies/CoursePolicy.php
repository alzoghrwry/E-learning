<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

 
    public function before(User $user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    // أي شخص يمكنه رؤية الكورس
    public function view(User $user, Course $course)
    {
        return true; // الكل يستطيع مشاهدة الكورسات
    }

    // فقط المدرس يمكنه إنشاء كورس
    public function create(User $user)
    {
        return $user->role === 'instructor';
    }

    // المدرس الذي يملك الكورس يمكنه التعديل
    public function update(User $user, Course $course)
    {
        return $user->id === $course->instructor_id;
    }

    // المدرس الذي يملك الكورس يمكنه الحذف
    public function delete(User $user, Course $course)
    {
        return $user->id === $course->instructor_id;
    }

    // لا حاجة لتعديلات إضافية لبقية الأساليب حالياً
}
