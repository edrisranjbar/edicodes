<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'amount',
        'currency',
        'message',
        'transaction_id',
        'payment_method',
        'card_number',
        'tracking_code',
        'ref_id',
        'status',
        'ip_address',
        'user_agent',
    ];
    
    /**
     * Get the formatted amount with currency
     *
     * @return string
     */
    public function getFormattedAmountAttribute()
    {
        $currency = $this->currency === 'T' ? 'تومان' : 'ریال';
        return number_format($this->amount) . ' ' . $currency;
    }
    
    /**
     * Get the status in Persian
     *
     * @return string
     */
    public function getStatusInPersianAttribute()
    {
        $statusMap = [
            'pending' => 'در انتظار پرداخت',
            'paid' => 'موفق',
            'failed' => 'ناموفق',
        ];
        
        return $statusMap[$this->status] ?? $this->status;
    }
    
    /**
     * Scope a query to only include successful donations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
    
    /**
     * Scope a query to only include pending donations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    /**
     * Scope a query to only include failed donations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
