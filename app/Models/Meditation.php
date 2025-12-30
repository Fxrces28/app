<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meditation extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'audio_path', 
        'duration', 'image_path', 'category_id', 'is_premium', 'play_count'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function plays(): HasMany
    {
        return $this->hasMany(MeditationPlay::class);
    }

    public function canBePlayedBy(?User $user = null): bool
    {

        if (!$this->is_premium) {
            return true;
        }
        
        if (!$user) {
            return false;
        }
        
        return $user->canAccessPremiumContent();
    }
}