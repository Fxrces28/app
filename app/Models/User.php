<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function hasActiveSubscription(): bool
{
    // Проверяем активную подписку
    if ($this->subscription && $this->subscription->is_active && $this->subscription->ends_at > now()) {
        return true;
    }
    
    // Проверяем активные платежи
    $hasActivePayment = Payment::where('user_id', $this->id)
        ->where('status', 'succeeded')
        ->where('subscription_ends_at', '>', now())
        ->exists();
    
    return $hasActivePayment || $this->isAdmin();
}

    public function canAccessPremiumContent(): bool
    {
        return $this->hasActiveSubscription() || $this->isAdmin();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
    public function getSubscriptionEndsAt()
{
    // Сначала проверяем активную подписку
    if ($this->subscription && $this->subscription->is_active) {
        return $this->subscription->ends_at;
    }
    
    // Если нет подписки, проверяем активные платежи
    $payment = Payment::where('user_id', $this->id)
        ->where('status', 'succeeded')
        ->where('subscription_ends_at', '>', now())
        ->latest()
        ->first();
        
    return $payment ? $payment->subscription_ends_at : null;
}
    
}