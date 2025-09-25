<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assignment;
use App\Models\User;
use App\Models\Submission;

class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        Assignment::all()->each(function ($assignment) {
            User::where('role','student')->inRandomOrder()->take(5)->get()
                ->each(fn($student) => Submission::factory()->create([
                    'assignment_id' => $assignment->id,
                    'student_id' => $student->id,
                ]));
        });
    }
}
