<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Shetabit\Payment\Facade\Payment;
use Shetabit\Payment\Exceptions\InvalidPaymentException;

class PaymentService
{
    /**
     * Create a payment request for course enrollment
     */
    public function createPaymentRequest(Enrollment $enrollment): array
    {
        try {
            $course = $enrollment->course;
            $user = $enrollment->user;
            
            // Create payment request
            $payment = Payment::via('zarinpal'); // You can change this to other gateways
            
            $payment->amount($enrollment->amount_paid * 10); // Convert to Rials (1 Toman = 10 Rials)
            $payment->detail([
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'user_id' => $user->id,
                'course_title' => $course->title,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ]);
            
            $payment->callback(route('payment.callback'));
            $payment->description("ثبت نام در دوره: {$course->title}");
            
            // Generate payment URL
            $paymentUrl = $payment->purchase();
            
            // Update enrollment with payment reference
            $enrollment->update([
                'payment_reference' => $payment->getTransactionId(),
                'payment_method' => 'zarinpal'
            ]);
            
            return [
                'success' => true,
                'payment_url' => $paymentUrl,
                'transaction_id' => $payment->getTransactionId(),
                'amount' => $enrollment->amount_paid,
                'currency' => 'Toman'
            ];
            
        } catch (\Exception $e) {
            Log::error('Payment creation failed: ' . $e->getMessage(), [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'خطا در ایجاد درخواست پرداخت',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Verify payment callback
     */
    public function verifyPayment(string $transactionId): array
    {
        try {
            $payment = Payment::via('zarinpal');
            $payment->transactionId($transactionId);
            
            // Verify the payment
            $receipt = $payment->verify();
            
            // Find enrollment by transaction ID
            $enrollment = Enrollment::where('payment_reference', $transactionId)->first();
            
            if (!$enrollment) {
                throw new \Exception('Enrollment not found for transaction: ' . $transactionId);
            }
            
            // Check if payment amount matches
            $expectedAmount = $enrollment->amount_paid * 10; // Convert to Rials
            if ($receipt->getAmount() != $expectedAmount) {
                throw new \Exception('Payment amount mismatch');
            }
            
            // Update enrollment status
            $enrollment->update([
                'payment_status' => 'completed',
                'paid_at' => now(),
                'payment_receipt' => $receipt->getReferenceId()
            ]);
            
            return [
                'success' => true,
                'enrollment' => $enrollment,
                'receipt_id' => $receipt->getReferenceId(),
                'amount' => $receipt->getAmount() / 10, // Convert back to Toman
                'message' => 'پرداخت با موفقیت انجام شد'
            ];
            
        } catch (InvalidPaymentException $e) {
            Log::error('Payment verification failed: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'پرداخت ناموفق بود',
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Payment verification error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'خطا در تایید پرداخت',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Refund payment
     */
    public function refundPayment(Enrollment $enrollment): array
    {
        try {
            // Check if payment can be refunded
            if ($enrollment->payment_status !== 'completed') {
                throw new \Exception('Payment is not completed');
            }
            
            if (!$enrollment->payment_receipt) {
                throw new \Exception('Payment receipt not found');
            }
            
            // Here you would implement refund logic with your payment gateway
            // For now, we'll just mark it as refunded
            $enrollment->update([
                'payment_status' => 'refunded',
                'refunded_at' => now()
            ]);
            
            return [
                'success' => true,
                'message' => 'بازپرداخت با موفقیت انجام شد',
                'enrollment' => $enrollment
            ];
            
        } catch (\Exception $e) {
            Log::error('Payment refund failed: ' . $e->getMessage(), [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'خطا در بازپرداخت',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get payment status
     */
    public function getPaymentStatus(Enrollment $enrollment): array
    {
        try {
            if (!$enrollment->payment_reference) {
                return [
                    'success' => false,
                    'message' => 'درخواست پرداخت یافت نشد'
                ];
            }
            
            $payment = Payment::via('zarinpal');
            $payment->transactionId($enrollment->payment_reference);
            
            // Get payment status from gateway
            $status = $payment->status();
            
            return [
                'success' => true,
                'status' => $status,
                'transaction_id' => $enrollment->payment_reference,
                'amount' => $enrollment->amount_paid,
                'currency' => 'Toman'
            ];
            
        } catch (\Exception $e) {
            Log::error('Payment status check failed: ' . $e->getMessage(), [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'خطا در بررسی وضعیت پرداخت',
                'error' => $e->getMessage()
            ];
        }
    }
}




