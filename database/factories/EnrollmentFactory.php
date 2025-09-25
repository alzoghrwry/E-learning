<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;
use App\Models\User;

class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'student_id' => User::factory()->create(['role' => 'student'])->id,
            'enrolled_at' => now(),
        ];
    }
}
