<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LessonPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    // أي شخص يمكنه مشاهدة الدرس
    public function view(User $user, Lesson $lesson)
    {
        // يمكن للطالب مشاهدة الدروس إذا كان مسجلاً في الكورس
        if ($user->role === 'student') {
            return $lesson->course->students->contains($user->id);
        }

        // المدرس أو أي صلاحية أخرى
        return $user->role === 'instructor';
    }

    // فقط المدرس الذي يملك الكورس يمكنه إنشاء درس
    public function create(User $user, Lesson $lesson)
    {
        return $user->id === $lesson->course->instructor_id;
    }

    // التعديل فقط من قبل المدرس صاحب الكورس
    public function update(User $user, Lesson $lesson)
    {
        return $user->id === $lesson->course->instructor_id;
    }

    public function delete(User $user, Lesson $lesson)
    {
        return $user->id === $lesson->course->instructor_id;
    }
}
