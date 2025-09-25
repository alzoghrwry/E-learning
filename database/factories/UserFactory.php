<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => fake()->randomElement(['admin','instructor','student']),
            'is_active' => true,
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'profile_photo' => fake()->imageUrl(200, 200, 'people'),
            'remember_token' => Str::random(10),
        ];
    }
}
