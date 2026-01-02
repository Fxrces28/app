<?php

namespace App\Http\Controllers;

use App\Models\Meditation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggle(Request $request)
    {
        $user = Auth::user();
        $meditationId = $request->meditation_id;
        
        try {
            if ($user->hasFavorite($meditationId)) {
                $user->removeFromFavorites($meditationId);
                $isFavorite = false;
                $message = 'Удалено из избранного';
            } else {
                $user->addToFavorites($meditationId);
                $isFavorite = true;
                $message = 'Добавлено в избранное';
            }
            
            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function add(Meditation $meditation)
    {
        try {
            $user = Auth::user();
            
            if ($user->addToFavorites($meditation->id)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Добавлено в избранное',
                    'is_favorite' => true
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Медитация уже в избранном'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function remove(Meditation $meditation)
    {
        try {
            $user = Auth::user();
            
            if ($user->removeFromFavorites($meditation->id)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Удалено из избранного',
                    'is_favorite' => false
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Медитация не была в избранном'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $user = Auth::user();
        $favorites = $user->favorites()
            ->with('category')
            ->paginate(12);
            
        return view('favorites.index', compact('favorites'));
    }

    public function list()
{
    try {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }
        
        $favoriteIds = $user->favorites()->pluck('meditations.id')->toArray();
        
        return response()->json([
            'success' => true,
            'favorites' => $favoriteIds
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ошибка: ' . $e->getMessage()
        ], 500);
    }
}
}