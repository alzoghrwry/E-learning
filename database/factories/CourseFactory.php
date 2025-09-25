<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'thumbnail' => fake()->imageUrl(640, 480, 'education'),
            'is_free' => fake()->boolean(40),
            'price' => fake()->optional()->randomFloat(2, 10, 200),
            'level' => fake()->randomElement(['beginner','intermediate','advanced']),
            'category' => fake()->word(),
            'instructor_id' => User::factory()->create(['role' => 'instructor'])->id,
            'category_id' => Category::factory(),
        ];
    }
}
