<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    protected $paymentService;
    
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    /**
     * Display a listing of user's enrollments.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لطفا ابتدا وارد شوید'
            ], 401);
        }

        $enrollments = $user->enrollments()
            ->with(['course.category', 'course.admin'])
            ->orderBy('enrolled_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $enrollments
        ]);
    }

    /**
     * Enroll in a course (create enrollment record).
     */
    public function enroll(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لطفا ابتدا وارد شوید'
            ], 401);
        }

        // Check if course is published
        if ($course->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'این دوره در دسترس نیست'
            ], 400);
        }

        // Check if already enrolled
        if ($course->isEnrolledBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'شما قبلا در این دوره ثبت نام کرده اید'
            ], 400);
        }

        // Create enrollment record
        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount_paid' => $course->price,
            'payment_status' => 'pending',
            'enrolled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ثبت نام در دوره با موفقیت انجام شد',
            'data' => [
                'enrollment' => $enrollment,
                'course' => $course,
                'payment_required' => $course->price > 0
            ]
        ], 201);
    }

    /**
     * Process payment for enrollment.
     */
    public function processPayment(Request $request, Enrollment $enrollment): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لطفا ابتدا وارد شوید'
            ], 401);
        }

        // Verify enrollment belongs to user
        if ($enrollment->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'دسترسی غیرمجاز'
            ], 403);
        }

        // Check if already paid
        if ($enrollment->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'پرداخت قبلا انجام شده است'
            ], 400);
        }

        // Check if course is free
        if ($enrollment->amount_paid === 0) {
            // Auto-complete free enrollment
            $enrollment->update([
                'payment_status' => 'completed',
                'paid_at' => now(),
                'payment_method' => 'free'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'ثبت نام در دوره رایگان با موفقیت انجام شد',
                'data' => [
                    'enrollment' => $enrollment->load('course'),
                    'payment_required' => false
                ]
            ]);
        }

        // Create payment request using PaymentService
        $paymentResult = $this->paymentService->createPaymentRequest($enrollment);
        
        if (!$paymentResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $paymentResult['message']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'درخواست پرداخت با موفقیت ایجاد شد',
            'data' => $paymentResult
        ]);
    }

    /**
     * Verify payment status.
     */
    public function verifyPayment(Enrollment $enrollment): JsonResponse
    {
        $user = request()->user();
        
        if (!$user || $enrollment->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'دسترسی غیرمجاز'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'enrollment' => $enrollment,
                'payment_status' => $enrollment->payment_status,
                'is_paid' => $enrollment->isPaid(),
                'is_active' => $enrollment->isActive()
            ]
        ]);
    }

    /**
     * Cancel enrollment (refund if applicable).
     */
    public function cancel(Request $request, Enrollment $enrollment): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لطفا ابتدا وارد شوید'
            ], 401);
        }

        // Verify enrollment belongs to user
        if ($enrollment->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'دسترسی غیرمجاز'
            ], 403);
        }

        // Check if can be cancelled
        if ($enrollment->payment_status === 'refunded') {
            return response()->json([
                'success' => false,
                'message' => 'این ثبت نام قبلا لغو شده است'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Here you would process refund if payment was completed
            if ($enrollment->isPaid()) {
                // Process refund logic here
                $enrollment->update(['payment_status' => 'refunded']);
            } else {
                // Just delete pending enrollment
                $enrollment->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ثبت نام با موفقیت لغو شد'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در لغو ثبت نام',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get enrollment statistics for admin.
     */
    public function statistics(Request $request): JsonResponse
    {
        // Check if user is authenticated
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لطفا ابتدا وارد شوید'
            ], 401);
        }

        // For now, allow any authenticated user to access statistics
        // In a real application, you might want to add admin role checking here

        $stats = [
            'total_enrollments' => Enrollment::count(),
            'completed_payments' => Enrollment::where('payment_status', 'completed')->count(),
            'pending_payments' => Enrollment::where('payment_status', 'pending')->count(),
            'total_revenue' => Enrollment::where('payment_status', 'completed')->sum('amount_paid'),
            'recent_enrollments' => Enrollment::with(['user', 'course'])
                ->orderBy('enrolled_at', 'desc')
                ->limit(10)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get all enrollments for admin (admin interface).
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Enrollment::with(['user', 'course.category']);

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('payment_status', $request->status);
        }

        if ($request->has('course_id') && $request->course_id !== '') {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('user_id') && $request->user_id !== '') {
            $query->where('user_id', $request->user_id);
        }

        $enrollments = $query->orderBy('enrolled_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $enrollments
        ]);
    }

    /**
     * Get specific enrollment for admin.
     */
    public function adminShow(Enrollment $enrollment): JsonResponse
    {
        $enrollment->load(['user', 'course.category', 'course.admin']);

        return response()->json([
            'success' => true,
            'data' => $enrollment
        ]);
    }

    /**
     * Update enrollment for admin.
     */
    public function adminUpdate(Request $request, Enrollment $enrollment): JsonResponse
    {
        $request->validate([
            'payment_status' => 'sometimes|in:pending,completed,failed,refunded',
            'amount_paid' => 'sometimes|numeric|min:0',
            'payment_method' => 'sometimes|string|max:255',
            'notes' => 'sometimes|string|max:1000'
        ]);

        try {
            $updateData = $request->only([
                'payment_status',
                'amount_paid',
                'payment_method'
            ]);

            // Set paid_at timestamp if status changed to completed
            if (isset($updateData['payment_status']) &&
                $updateData['payment_status'] === 'completed' &&
                $enrollment->payment_status !== 'completed') {
                $updateData['paid_at'] = now();
            }

            $enrollment->update($updateData);

            // Log admin action if notes provided
            if ($request->has('notes')) {
                // You might want to create an admin action log here
                \Illuminate\Support\Facades\Log::info('Enrollment updated by admin', [
                    'enrollment_id' => $enrollment->id,
                    'admin_id' => $request->user()->id,
                    'changes' => $updateData,
                    'notes' => $request->notes
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'ثبت نام با موفقیت به‌روزرسانی شد',
                'data' => $enrollment->load(['user', 'course'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در به‌روزرسانی ثبت نام',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete enrollment for admin.
     */
    public function adminDestroy(Enrollment $enrollment): JsonResponse
    {
        try {
            // Check if enrollment can be deleted
            if ($enrollment->payment_status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'نمی‌توان ثبت نام پرداخت شده را حذف کرد'
                ], 400);
            }

            $enrollment->delete();

            return response()->json([
                'success' => true,
                'message' => 'ثبت نام با موفقیت حذف شد'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در حذف ثبت نام',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
