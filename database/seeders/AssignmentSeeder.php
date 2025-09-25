<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Assignment;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        Course::all()->each(function ($course) {
            Assignment::factory(3)->create(['course_id' => $course->id]);
        });
    }
}
