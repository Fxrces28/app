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
        $hasActivePayment = Payment::where('user_id', $this->id)
            ->where('status', 'succeeded')
            ->where('subscription_ends_at', '>', now())
            ->exists();
        
        if (!$hasActivePayment) {
            $hasActivePayment = $this->subscription && 
                            $this->subscription->is_active && 
                            $this->subscription->ends_at > now();
        }
        
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
    
}