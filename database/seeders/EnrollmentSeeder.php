<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        Course::all()->each(function ($course) {
            User::where('role','student')->inRandomOrder()->take(10)->get()
                ->each(fn($student) => Enrollment::factory()->create([
                    'course_id' => $course->id,
                    'student_id' => $student->id,
                ]));
        });
    }
}
