<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PersianDateHelper;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class DonationController extends Controller
{
    /**
     * Get paid donations for public display
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicIndex(Request $request)
    {
        // Get only paid donations, sorted by created_at descending
        $perPage = $request->input('per_page', 10);
        
        $donations = Donation::where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        // Transform donation data to show only public info
        $donations->getCollection()->transform(function($donation) {
            return [
                'id' => $donation->id,
                'name' => $donation->name ?? 'ناشناس',
                'amount' => $donation->amount,
                'formatted_amount' => $donation->formatted_amount,
                'message' => $donation->message,
                'created_at' => $donation->created_at->format('Y-m-d'),
                'created_at_human' => PersianDateHelper::timeAgo($donation->created_at),
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $donations
        ]);
    }

    /**
     * Get all donations (admin only)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Set default params and get request params
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status', 'all');
        $sort = $request->input('sort', '-created_at');
        
        // Start query
        $query = Donation::query();
        
        // Apply status filter if specified
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        // Apply sorting
        if ($sort) {
            $direction = 'asc';
            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $sort = substr($sort, 1);
            }
            $query->orderBy($sort, $direction);
        }
        
        // Get paginated results
        $donations = $query->paginate($perPage);
        
        // Transform donation data with additional attributes
        $donations->getCollection()->transform(function($donation) {
            $donation->formatted_amount = $donation->formatted_amount;
            $donation->status_in_persian = $donation->status_in_persian;
            return $donation;
        });
        
        return response()->json($donations);
    }

    /**
     * Create a new payment for donation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pay(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1000',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'لطفاً اطلاعات را به درستی وارد کنید',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get validated data
            $data = $validator->validated();
            
            // Create donation record
            $donation = Donation::create([
                'name' => $data['name'] ?? 'ناشناس',
                'email' => $data['email'] ?? null,
                'message' => $data['message'] ?? null,
                'amount' => $data['amount'],
                'currency' => 'T', // Toman
                'payment_method' => 'zarinpal',
                'status' => 'pending',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Create invoice
            $invoice = new Invoice;
            $invoice->amount((int)$data['amount']);
            $invoice->detail('donor', $donation->id);

            Log::info('Initiating donation payment', [
                'donation_id' => $donation->id,
                'amount' => $donation->amount,
                'ip_address' => $donation->ip_address,
                'user_agent' => $donation->user_agent,
                'created_at' => $donation->created_at,
            ]);
            
            // Purchase the invoice and get the payment form
            $paymentRef = Payment::callbackUrl(route('donations.verify'))
                ->purchase(
                    $invoice,
                    function($driver, $transactionId) use ($donation) {
                        // Store the transaction ID
                        $donation->update([
                            'transaction_id' => $transactionId
                        ]);
                    }
                );
            
            // Get the payment form (Zarinpal returns a RedirectionForm here)
            $redirectForm = $paymentRef->pay();
            
            // For Zarinpal, we need to get the action from the redirection form
            $paymentUrl = $redirectForm->getAction();
            
            // Check if payment URL is empty
            if (empty($paymentUrl)) {
                Log::error('Payment URL is empty', [
                    'donation_id' => $donation->id,
                    'redirect_form' => $redirectForm,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'خطا در اتصال به درگاه پرداخت. لطفاً مجدد تلاش کنید.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'در حال انتقال به درگاه پرداخت...',
                'payment_url' => $paymentUrl,
                'donation_id' => $donation->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Donation payment error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطا در اتصال به درگاه پرداخت. لطفاً مجدد تلاش کنید.',
            ], 500);
        }
    }

    /**
     * Verify the payment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        try {
            // Find related donation by Authority - handle both Authority and authority (case-insensitive)
            $authority = $request->Authority ?? $request->authority ?? '';
            
            if (empty($authority)) {
                return response()->json([
                    'success' => false,
                    'message' => 'اطلاعات پرداخت ناقص است'
                ], 400);
            }
            
            $donation = Donation::where('transaction_id', $authority)->first();

            // Check if donation exists
            if (!$donation) {
                return response()->json([
                    'success' => false,
                    'message' => 'اطلاعات پرداخت یافت نشد.'
                ], 404);
            }

            // Get Status from request - handle both Status and status (case-insensitive)
            $status = $request->Status ?? $request->status ?? '';
            
            // Convert status to uppercase for consistency
            $status = strtoupper($status);

            // Check Status (if payment was successful)
            if ($status !== 'OK') {
                $donation->update([
                    'status' => 'failed'
                ]);

                // If the request was from a browser, redirect to frontend
                if ($request->header('Accept') && str_contains($request->header('Accept'), 'text/html')) {
                    $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
                    return redirect()->to("{$frontendUrl}/donation/success?status=failed&authority={$authority}");
                }

                return response()->json([
                    'success' => false,
                    'message' => 'پرداخت ناموفق یا لغو شده توسط کاربر.'
                ]);
            }

            // Verify payment if successful
            $receipt = Payment::amount($donation->amount)
                ->transactionId($authority)
                ->verify();

            // Get reference ID
            $refId = $receipt->getReferenceId();

            // Update donation
            $donation->update([
                'status' => 'paid',
                'ref_id' => $refId
            ]);

            // Format donation amount with Persian numerals for response
            $formattedAmount = number_format($donation->amount) . ' تومان';
            
            // If the request was from a browser, redirect to frontend
            if ($request->header('Accept') && str_contains($request->header('Accept'), 'text/html')) {
                $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
                return redirect()->to("{$frontendUrl}/donation/success?status=success&authority={$authority}");
            }

            return response()->json([
                'success' => true,
                'message' => 'پرداخت شما با موفقیت انجام شد. از حمایت شما سپاسگزاریم.',
                'donation' => [
                    'id' => $donation->id,
                    'amount' => $formattedAmount,
                    'ref_id' => $refId,
                    'created_at' => $donation->created_at->format('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Payment verification error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->paymentError('خطا در بررسی پرداخت: ' . $e->getMessage());
        }
    }
    
    /**
     * Return error response for payment
     *
     * @param string $message
     * @param Donation|null $donation
     * @return \Illuminate\Http\Response
     */
    private function paymentError($message, Donation $donation = null)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($donation) {
            $response['donation'] = [
                'id' => $donation->id,
                'amount' => $donation->formatted_amount,
                'status' => $donation->status_in_persian,
            ];
        }

        return response()->json($response);
    }

    /**
     * Get all donations for admin (admin interface).
     */
    public function adminIndex(Request $request)
    {
        // Set default params and get request params
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status', 'all');
        $sort = $request->input('sort', '-created_at');

        // Start query
        $query = Donation::query();

        // Apply status filter if specified
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Apply date range filter if specified
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Apply sorting
        if ($sort) {
            $direction = 'asc';
            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $sort = substr($sort, 1);
            }
            $query->orderBy($sort, $direction);
        }

        // Get paginated results
        $donations = $query->paginate($perPage);

        // Transform donation data with additional attributes
        $donations->getCollection()->transform(function($donation) {
            $donation->formatted_amount = $donation->formatted_amount;
            $donation->status_in_persian = $donation->status_in_persian;
            return $donation;
        });

        return response()->json([
            'success' => true,
            'data' => $donations
        ]);
    }

    /**
     * Get specific donation for admin.
     */
    public function adminShow(Donation $donation)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $donation->id,
                'name' => $donation->name,
                'email' => $donation->email,
                'message' => $donation->message,
                'amount' => $donation->amount,
                'formatted_amount' => $donation->formatted_amount,
                'currency' => $donation->currency,
                'payment_method' => $donation->payment_method,
                'status' => $donation->status,
                'status_in_persian' => $donation->status_in_persian,
                'transaction_id' => $donation->transaction_id,
                'ref_id' => $donation->ref_id,
                'ip_address' => $donation->ip_address,
                'user_agent' => $donation->user_agent,
                'created_at' => $donation->created_at,
                'updated_at' => $donation->updated_at
            ]
        ]);
    }

    /**
     * Update donation for admin.
     */
    public function adminUpdate(Request $request, Donation $donation)
    {
        $request->validate([
            'status' => 'sometimes|in:pending,paid,failed',
            'notes' => 'sometimes|string|max:1000'
        ]);

        try {
            $updateData = $request->only(['status']);

            $donation->update($updateData);

            // Log admin action if notes provided
            if ($request->has('notes')) {
                Log::info('Donation updated by admin', [
                    'donation_id' => $donation->id,
                    'admin_id' => $request->user()->id ?? 'system',
                    'changes' => $updateData,
                    'notes' => $request->notes
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'کمک مالی با موفقیت به‌روزرسانی شد',
                'data' => $donation->load('formatted_amount', 'status_in_persian')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در به‌روزرسانی کمک مالی',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete donation for admin.
     */
    public function adminDestroy(Donation $donation)
    {
        try {
            // Only allow deletion of pending donations
            if ($donation->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'نمی‌توان کمک مالی پرداخت شده را حذف کرد'
                ], 400);
            }

            $donation->delete();

            return response()->json([
                'success' => true,
                'message' => 'کمک مالی با موفقیت حذف شد'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در حذف کمک مالی',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
