<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meditation;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class PublicMeditationController extends Controller
{
    public function index(Request $request)
    {
        $query = Meditation::with('category');
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->filled('type')) {
            if ($request->type == 'free') {
                $query->where('is_premium', false);
            } elseif ($request->type == 'premium') {
                $query->where('is_premium', true);
            }
        }
        
        switch ($request->get('sort', 'newest')) {
            case 'popular':
                $query->orderBy('play_count', 'desc');
                break;
            case 'duration':
                $query->orderBy('duration');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }
        
        $meditations = $query->paginate(12);
        $categories = Category::all();
        
        return view('meditations.index', compact('meditations', 'categories'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Meditation $meditation)
{
    $user = Auth::user();
    
    if ($meditation->is_premium) {
        if (!$user) {

            return redirect()->route('subscriptions.plans')
                ->with('error', 'Для доступа к этой медитации требуется подписка. Зарегистрируйтесь или войдите в аккаунт.')
                ->with('redirect_to', route('meditations.show', $meditation));
            
        }
        
        if (!$user->canAccessPremiumContent()) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Для доступа к этой медитации требуется подписка')
                ->with('redirect_to', route('meditations.show', $meditation));
        }
    }
    
    $meditation->increment('play_count');
    
    $relatedMeditations = Meditation::where('category_id', $meditation->category_id)
        ->where('id', '!=', $meditation->id)
        ->where(function($query) use ($user) {
            if (!$user || !$user->hasActiveSubscription()) {
                $query->where('is_premium', false);
            }
        })
        ->limit(4)
        ->get();
        
    return view('meditations.show', compact('meditation', 'relatedMeditations'));
}

    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')
                    ->withTimestamps();
    }
}
