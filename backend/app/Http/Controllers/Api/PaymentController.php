<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;
    
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    /**
     * Create payment request for enrollment
     */
    public function createPayment(Request $request, Enrollment $enrollment): JsonResponse
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
        
        // Create payment request
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
     * Payment callback from gateway
     */
    public function callback(Request $request): JsonResponse
    {
        try {
            $transactionId = $request->input('Authority') ?? $request->input('transaction_id');
            
            if (!$transactionId) {
                Log::error('Payment callback missing transaction ID', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => 'شناسه تراکنش یافت نشد'
                ], 400);
            }
            
            // Verify payment
            $verificationResult = $this->paymentService->verifyPayment($transactionId);
            
            if (!$verificationResult['success']) {
                Log::error('Payment verification failed', [
                    'transaction_id' => $transactionId,
                    'result' => $verificationResult
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $verificationResult['message']
                ], 400);
            }
            
            $enrollment = $verificationResult['enrollment'];
            
            // Redirect to success page or return success response
            return response()->json([
                'success' => true,
                'message' => $verificationResult['message'],
                'data' => [
                    'enrollment' => $enrollment->load('course'),
                    'receipt_id' => $verificationResult['receipt_id'],
                    'amount' => $verificationResult['amount']
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در پردازش بازگشت پرداخت',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check payment status
     */
    public function checkStatus(Request $request, Enrollment $enrollment): JsonResponse
    {
        $user = $request->user();
        
        if (!$user || $enrollment->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'دسترسی غیرمجاز'
            ], 403);
        }
        
        $statusResult = $this->paymentService->getPaymentStatus($enrollment);
        
        return response()->json([
            'success' => $statusResult['success'],
            'message' => $statusResult['message'] ?? null,
            'data' => $statusResult
        ]);
    }
    
    /**
     * Refund payment
     */
    public function refund(Request $request, Enrollment $enrollment): JsonResponse
    {
        $user = $request->user();
        
        if (!$user || $enrollment->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'دسترسی غیرمجاز'
            ], 403);
        }
        
        // Check if refund is allowed (e.g., within 24 hours)
        $hoursSincePayment = $enrollment->paid_at ? now()->diffInHours($enrollment->paid_at) : 0;
        
        if ($hoursSincePayment > 24) {
            return response()->json([
                'success' => false,
                'message' => 'بازپرداخت فقط تا 24 ساعت پس از پرداخت امکان پذیر است'
            ], 400);
        }
        
        $refundResult = $this->paymentService->refundPayment($enrollment);
        
        return response()->json([
            'success' => $refundResult['success'],
            'message' => $refundResult['message'],
            'data' => $refundResult['enrollment'] ?? null
        ]);
    }
    
    /**
     * Get payment methods
     */
    public function getPaymentMethods(): JsonResponse
    {
        $methods = [
            [
                'id' => 'zarinpal',
                'name' => 'زرین‌پال',
                'description' => 'پرداخت امن با زرین‌پال',
                'logo' => '/images/payment/zarinpal.png',
                'enabled' => true
            ],
            [
                'id' => 'nextpay',
                'name' => 'نکست‌پی',
                'description' => 'پرداخت سریع با نکست‌پی',
                'logo' => '/images/payment/nextpay.png',
                'enabled' => false // Enable when you add NextPay support
            ],
            [
                'id' => 'parsijoo',
                'name' => 'پارس‌ایجو',
                'description' => 'پرداخت با پارس‌ایجو',
                'logo' => '/images/payment/parsijoo.png',
                'enabled' => false // Enable when you add Parsijoo support
            ]
        ];
        
        return response()->json([
            'success' => true,
            'data' => $methods
        ]);
    }
}















