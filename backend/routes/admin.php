<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\PageViewController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\AdminAuthController;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "admin" middleware group. Make something great!
|
*/

// Admin Authentication Routes (Public - no auth required for login)
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login'])
    ->name('admin.login')
    ->middleware('throttle:10,60');
});

// Protected Admin Routes (require authentication)
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index']);
    
    // Admin Profile Management
    Route::get('/profile', [\App\Http\Controllers\Api\AdminAuthController::class, 'profile']);
    Route::post('/change-password', [\App\Http\Controllers\Api\AdminAuthController::class, 'changePassword']);
    Route::post('/update-profile', [\App\Http\Controllers\Api\AdminAuthController::class, 'updateProfile']);
    
    // Admin Course Management Routes
    Route::get('/courses', [CourseController::class, 'adminIndex']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{course}', [CourseController::class, 'adminShow']);
    Route::put('/courses/{course}', [CourseController::class, 'update']);
    Route::delete('/courses/{course}', [CourseController::class, 'destroy']);
    
    // Admin Course Content Management
    Route::get('/courses/{course}/contents', [\App\Http\Controllers\Api\CourseContentController::class, 'index']);
    Route::post('/courses/{course}/contents', [\App\Http\Controllers\Api\CourseContentController::class, 'store']);
    Route::post('/courses/{course}/contents/with-file', [\App\Http\Controllers\Api\CourseContentController::class, 'storeWithFile']);
    Route::get('/courses/{course}/contents/{content}', [\App\Http\Controllers\Api\CourseContentController::class, 'show']);
    Route::get('/courses/{course}/contents/{content}/files', [\App\Http\Controllers\Api\CourseContentController::class, 'getContentWithFiles']);
    Route::put('/courses/{course}/contents/{content}', [\App\Http\Controllers\Api\CourseContentController::class, 'update']);
    Route::delete('/courses/{course}/contents/{content}', [\App\Http\Controllers\Api\CourseContentController::class, 'destroy']);
    Route::post('/courses/{course}/contents/reorder', [\App\Http\Controllers\Api\CourseContentController::class, 'reorder']);
    Route::post('/courses/{course}/contents/upload-video', [\App\Http\Controllers\Api\CourseContentController::class, 'uploadVideo']);
    Route::post('/courses/{course}/contents/upload-file', [\App\Http\Controllers\Api\CourseContentController::class, 'uploadFile']);
    
    // Admin Blog Management Routes
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('posts', PostController::class);
    
    // Admin Comment Management Routes
    Route::get('/comments', [CommentController::class, 'index']);
    Route::get('/comments/{comment}', [CommentController::class, 'show']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::patch('/comments/{comment}/status', [CommentController::class, 'updateStatus']);
    Route::post('/comments/{comment}/reply', [CommentController::class, 'adminReply']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    
    // Admin Enrollment Management
    Route::get('/enrollments', [\App\Http\Controllers\Api\EnrollmentController::class, 'adminIndex']);
    Route::get('/enrollments/statistics', [EnrollmentController::class, 'statistics']);
    Route::get('/enrollments/{enrollment}', [\App\Http\Controllers\Api\EnrollmentController::class, 'adminShow']);
    Route::put('/enrollments/{enrollment}', [\App\Http\Controllers\Api\EnrollmentController::class, 'adminUpdate']);
    Route::delete('/enrollments/{enrollment}', [\App\Http\Controllers\Api\EnrollmentController::class, 'adminDestroy']);
    
    // Admin Donation Management
    Route::get('/donations', [DonationController::class, 'adminIndex']);
    Route::get('/donations/{donation}', [DonationController::class, 'adminShow']);
    Route::put('/donations/{donation}', [DonationController::class, 'adminUpdate']);
    Route::delete('/donations/{donation}', [DonationController::class, 'adminDestroy']);
    
    // Admin Analytics
    Route::get('/analytics', [PageViewController::class, 'getAnalytics']);
    Route::get('/posts/{postId}/analytics', [PageViewController::class, 'getPostAnalytics']);
    
    // Admin File Uploads
    Route::post('/upload/course-thumbnail', [UploadController::class, 'uploadCourseThumbnail']);
    Route::post('/upload/image', [UploadController::class, 'uploadImage']);

    // Admin Logout
    Route::post('/logout', [AdminAuthController::class, 'logout']);
});
