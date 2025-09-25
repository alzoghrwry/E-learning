<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\LessonController;
use App\Http\Controllers\API\AssignmentController;
use App\Http\Controllers\API\SubmissionsController;
use App\Http\Controllers\API\EnrollmentController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Api\ChatController;

Route::prefix('v1')->group(function () {
  
    Route::get('courses/public', [CourseController::class, 'publicCourses']);
    Route::get('categories/public', [CategoryController::class, 'publicCategories']);

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

  
   
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('lessons', LessonController::class);
        Route::apiResource('assignments', AssignmentController::class);
        Route::apiResource('submissions', SubmissionsController::class);
        Route::apiResource('enrollments', EnrollmentController::class);
        Route::get('instructors', [UserController::class, 'instructors']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

   
    Route::middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {
        Route::apiResource('categories', CategoryController::class);
    });
});


