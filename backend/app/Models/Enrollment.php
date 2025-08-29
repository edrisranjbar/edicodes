<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'amount_paid',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_receipt',
        'enrolled_at',
        'paid_at',
        'refunded_at',
        'expires_at'
    ];

    protected $casts = [
        'amount_paid' => 'integer',
        'enrolled_at' => 'datetime',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    protected $attributes = [
        'payment_status' => 'pending'
    ];

    /**
     * Get the user that owns the enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that the user is enrolled in.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if the enrollment is active.
     */
    public function isActive(): bool
    {
        if (!$this->expires_at) {
            return true; // No expiration
        }
        return now()->lt($this->expires_at);
    }

    /**
     * Check if the payment is completed.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }
}
