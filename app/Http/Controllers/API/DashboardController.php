<?php
// App\Http\Controllers\API\DashboardController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;

class DashboardController extends Controller
{
    public function stats()
    {
        $studentsCount = User::where('role', 'student')->count();
        $coursesCount = Course::count();
        $averageRating = Course::avg('rating'); // لازم يكون عندك عمود rating في جدول الكورسات

        return response()->json([
            'students' => $studentsCount,
            'courses' => $coursesCount,
            'average_rating' => round($averageRating, 1)
        ]);
    }
}

