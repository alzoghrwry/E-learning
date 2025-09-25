<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;

class LessonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraph(),
            'video_url' => fake()->url(),
            'order' => fake()->numberBetween(1, 10),
        ];
    }
}
