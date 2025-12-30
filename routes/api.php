<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MeditationController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Публичные маршруты
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

// Медитации
Route::get('/meditations', [MeditationController::class, 'index']);
Route::get('/meditations/{id}', [MeditationController::class, 'show']);
Route::get('/categories', [MeditationController::class, 'categories']);
Route::get('/tags', [MeditationController::class, 'tags']);

// Подписки
Route::get('/subscription-plans', [SubscriptionController::class, 'plans']);

// Защищённые маршруты
Route::middleware('auth:sanctum')->group(function () {
    // Аутентификация
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'updateProfile']);
    
    // Медитации
    Route::post('/meditations/{id}/play', [MeditationController::class, 'recordPlay']);
    Route::post('/meditations/{id}/favorite', [MeditationController::class, 'toggleFavorite']);
    Route::get('/favorites', [MeditationController::class, 'favorites']);
    Route::get('/recommended', [MeditationController::class, 'recommended']);
    
    // Подписки
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::get('/subscription', [SubscriptionController::class, 'status']);
    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancel']);
    
    // История прослушиваний
    Route::get('/history', [MeditationController::class, 'history']);
    
    // Уведомления
    Route::get('/notifications', [UserController::class, 'notifications']);
    Route::post('/notifications/{id}/read', [UserController::class, 'markAsRead']);
});