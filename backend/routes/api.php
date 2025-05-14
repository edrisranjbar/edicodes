<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\TestResendController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    
    // Protected admin routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/profile', [AdminAuthController::class, 'profile']);
        Route::post('/change-password', [AdminAuthController::class, 'changePassword']);
        Route::post('/upload/image', [UploadController::class, 'uploadImage']);
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);
        
        // Donations
        Route::get('/donations', [DonationController::class, 'index'])->name('admin.donations.index');
        
        // Blog Management Routes
        Route::apiResource('categories', CategoryController::class)
            ->names([
                'index' => 'admin.categories.index',
                'store' => 'admin.categories.store',
                'show' => 'admin.categories.show',
                'update' => 'admin.categories.update',
                'destroy' => 'admin.categories.destroy',
            ]);
            
        Route::apiResource('posts', PostController::class)
            ->names([
                'index' => 'admin.posts.index',
                'store' => 'admin.posts.store',
                'show' => 'admin.posts.show',
                'update' => 'admin.posts.update',
                'destroy' => 'admin.posts.destroy',
            ]);
            
        // Comment Management Routes
        Route::get('/comments', [CommentController::class, 'index']);
        Route::get('/comments/{comment}', [CommentController::class, 'show']);
        Route::put('/comments/{comment}', [CommentController::class, 'update']);
        Route::patch('/comments/{comment}/status', [CommentController::class, 'updateStatus']);
        Route::post('/comments/{comment}/reply', [CommentController::class, 'adminReply']);
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    });
});

// Contact Form Endpoint
Route::post('/contact', [ContactController::class, 'store']);
// Alternative endpoint to avoid ad-blockers
Route::post('/send-message', [ContactController::class, 'store']);

// Donation Endpoints
Route::post('/donations/pay', [DonationController::class, 'pay']);
Route::get('/donations/verify', [DonationController::class, 'verify'])->name('donations.verify');

// Test Resend Email (only in local/development environment)
if (app()->environment(['local', 'development'])) {
    Route::get('/test-email', [TestResendController::class, 'testEmail']);
}

// Health Check Endpoint
Route::get('/health', function() {
    return response()->json([
        'success' => true,
        'message' => 'سیستم فعال است',
        'status' => 'UP'
    ]);
});

// Public Blog routes 
Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class)->only(['index', 'show']);
Route::apiResource('posts', \App\Http\Controllers\Api\PostController::class)->only(['index', 'show']);
Route::get('posts/slug/{slug}', [\App\Http\Controllers\Api\PostController::class, 'findBySlug']); 

// Comment Routes
Route::post('/comments', [CommentController::class, 'store']);
Route::get('/posts/{post}/comments', [CommentController::class, 'postComments']); 