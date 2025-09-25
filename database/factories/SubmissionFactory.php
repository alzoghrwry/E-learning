<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Assignment;
use App\Models\User;

class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'assignment_id' => Assignment::factory(),
            'student_id' => User::factory()->create(['role' => 'student'])->id,
            'file_path' => fake()->filePath(),
            'feedback' => fake()->optional()->sentence(),
            'grade' => fake()->optional()->numberBetween(50, 100),
        ];
    }
}
