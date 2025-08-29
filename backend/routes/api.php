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
use App\Http\Controllers\Api\PageViewController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CourseContentController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\VideoStreamController;
use App\Http\Controllers\Api\PaymentController;

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

// Contact Form Endpoint
Route::post('/send-message', [ContactController::class, 'store'])->middleware('throttle:2,60');

// Donation Endpoints
Route::post('/donations/pay', [DonationController::class, 'pay']);
Route::get('/donations/verify', [DonationController::class, 'verify'])->name('donations.verify');

// Test Resend Email (only in local/development environment)
if (app()->environment(['local', 'development'])) {
    Route::get('/test-email', [TestResendController::class, 'testEmail']);
    
    // Payment Test Routes (Development Only)
    Route::prefix('test')->group(function () {
        Route::get('/payment/methods', [PaymentController::class, 'getPaymentMethods']);
        Route::post('/payment/create-test-enrollment', function() {
            // Create a test enrollment for testing payments
            $course = \App\Models\Course::first();
            $user = \App\Models\User::first();
            
            if (!$course || !$user) {
                return response()->json(['error' => 'No course or user found'], 400);
            }
            
            $enrollment = \App\Models\Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount_paid' => 1000, // 1000 Toman
                'payment_status' => 'pending',
                'enrolled_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'enrollment' => $user->id,
                'message' => 'Test enrollment created'
            ]);
        });
    });
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
Route::post('/comments', [CommentController::class, 'store'])->middleware('throttle:3,5');
Route::get('/posts/{post}/comments', [CommentController::class, 'postComments']);

// Record a page view (for manual tracking if needed)
Route::post('/posts/{postId}/view', [PageViewController::class, 'recordPostView']);
Route::post('/page-view', [PageViewController::class, 'recordGenericPageView']);

// Public Course Routes
Route::apiResource('courses', CourseController::class)->only(['index', 'show']);
Route::get('courses/slug/{course:slug}', [CourseController::class, 'show']);

// Payment Callback (Public - called by payment gateway)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Protected Course Routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // Course Content Access
    Route::get('/courses/{course}/contents/{content}', [CourseController::class, 'getContent']);
    Route::get('/courses/{course}/contents/{content}/video', [VideoStreamController::class, 'stream'])->name('api.courses.video');
    
    // Enrollment Routes
    Route::get('/enrollments', [EnrollmentController::class, 'index']);
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll']);
    Route::post('/enrollments/{enrollment}/payment', [EnrollmentController::class, 'processPayment']);
    Route::get('/enrollments/{enrollment}/verify', [EnrollmentController::class, 'verifyPayment']);
    Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'cancel']);
    
    // Payment Routes
    Route::get('/payment/methods', [PaymentController::class, 'getPaymentMethods']);
    Route::post('/enrollments/{enrollment}/create-payment', [PaymentController::class, 'createPayment']);
    Route::get('/enrollments/{enrollment}/payment-status', [PaymentController::class, 'checkStatus']);
    Route::post('/enrollments/{enrollment}/refund', [PaymentController::class, 'refund']);
    

});

