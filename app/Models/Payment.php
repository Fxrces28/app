<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'yookassa_payment_id',
        'status',
        'amount',
        'currency',
        'description',
        'metadata',
        'paid_at',
        'captured_at',
        'expires_at',
        'subscription_ends_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'captured_at' => 'datetime',
        'expires_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'succeeded';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'canceled';
    }
    
    public function hasActiveSubscription(): bool
    {
        return $this->status === 'succeeded' && 
               $this->subscription_ends_at && 
               $this->subscription_ends_at > now();
    }
}