<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        Course::all()->each(function ($course) {
            Lesson::factory(5)->create(['course_id' => $course->id]);
        });
    }
}

