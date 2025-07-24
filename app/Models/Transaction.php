<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'purpose', // Purpose of the transaction (e.g., order payment, refund, etc.)
        'order_id', // Associated order ID
        'transaction_amount',
        'currency', // Default currency
        'provider', // Payment provider, default to Stripe
        'stripe_session_id', // Stripe session ID for payment
        'status', // Transaction status
        'description', // Description of the transaction
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
