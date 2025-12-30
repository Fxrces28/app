<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicMeditationController;
use App\Http\Controllers\PublicSubscriptionController;
use App\Http\Controllers\PaymentController;

require __DIR__.'/auth.php';
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about-app', [HomeController::class, 'about'])->name('about.app');

Route::get('/meditations', [PublicMeditationController::class, 'index'])->name('meditations.index');
Route::get('/meditations/{meditation}', [PublicMeditationController::class, 'show'])->name('meditations.show');

Route::get('/subscription-plans', [PublicSubscriptionController::class, 'plans'])->name('subscriptions.plans');

Route::post('/activate-free-subscription', [PublicSubscriptionController::class, 'activateFree'])
    ->name('subscriptions.activate-free')
    ->middleware('auth');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'adminDashboard'])->name('dashboard');
    Route::resource('meditations', \App\Http\Controllers\Admin\MeditationController::class);
    Route::resource('subscriptions', \App\Http\Controllers\Admin\SubscriptionPlanController::class);
    Route::get('analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');

    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class);
});

Route::middleware('auth')->prefix('payment')->name('payment.')->group(function () {
    Route::get('/checkout/{plan}', [PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/create/{plan}', [PaymentController::class, 'create'])->name('create');
    
    Route::post('/test/{plan}', [PaymentController::class, 'testCreate'])->name('test');
    
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    
    Route::post('/webhook', [PaymentController::class, 'webhook'])
        ->name('webhook')
        ->withoutMiddleware(['auth']);
});