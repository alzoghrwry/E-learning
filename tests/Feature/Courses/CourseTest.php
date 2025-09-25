<?php

namespace Tests\Feature\Courses;

use App\Models\Course;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instructor = User::factory()->create(['role' => 'instructor']);
        $this->category = Category::factory()->create();
    }

    #[Test]
    public function it_lists_courses(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->getJson('/courses');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $course->id,
                     'title' => $course->title,
                 ]);
    }

    #[Test]
    public function it_creates_a_course(): void
    {
        $payload = [
            'title' => 'New Course',
            'description' => 'Test Description',
            'price' => 100,
            'is_free' => false,
            'level' => 'beginner',
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
        ];

        $response = $this->postJson('/courses', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'New Course']);

        $this->assertDatabaseHas('courses', ['title' => 'New Course']);
    }

    #[Test]
    public function it_shows_a_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->getJson("/courses/{$course->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => $course->title]);
    }

    #[Test]
    public function it_updates_a_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
        ]);

        $payload = ['title' => 'Updated Title'];

        $response = $this->putJson("/courses/{$course->id}", $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Updated Title']);

        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'Updated Title']);
    }

    #[Test]
    public function it_deletes_a_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->deleteJson("/courses/{$course->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}
