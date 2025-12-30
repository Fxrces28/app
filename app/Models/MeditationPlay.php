<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeditationPlay extends Model
{
    protected $fillable = [
        'user_id', 
        'meditation_id', 
        'played_at', 
        'duration_played',
        'device_type',
        'ip_address'
    ];

    protected $casts = [
        'played_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meditation(): BelongsTo
    {
        return $this->belongsTo(Meditation::class);
    }
}