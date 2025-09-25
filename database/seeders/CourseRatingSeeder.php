<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseRatingSeeder extends Seeder
{
    public function run(): void
    {
        Course::all()->each(function ($course) {
            $course->rating = rand(1, 5);
            $course->save();
        });

        $this->command->info('تم تحديث تقييمات الكورسات بنجاح!');
    }
}
