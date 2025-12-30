<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration',
        'duration_days',
        'features',
        'is_active',
        'is_popular',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'features' => 'array',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->isFree() 
            ? 'Бесплатно' 
            : number_format($this->price, 2) . ' ₽';
    }

    public function getDurationTextAttribute(): string
    {
        return match($this->duration) {
            'month' => 'Месяц',
            '6months' => '6 месяцев',
            'year' => 'Год',
            default => $this->duration
        };
    }
}