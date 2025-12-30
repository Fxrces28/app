<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meditation;
use App\Models\Category;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $topMeditations = Meditation::orderBy('play_count', 'desc')
            ->limit(10)
            ->get();

        $meditationsByCategory = Category::withCount('meditations')
            ->orderBy('meditations_count', 'desc')
            ->get();

        $totalUsers = User::count();
    
        $activeSubscriptions = 0;
        try {
            $activeSubscriptions = Payment::where('status', 'succeeded')
                ->where('subscription_ends_at', '>', now())
                ->distinct('user_id')
                ->count('user_id');
                
            if ($activeSubscriptions === 0) {
                $activeSubscriptions = Subscription::where('is_active', true)
                    ->where('ends_at', '>', now())
                    ->distinct('user_id')
                    ->count('user_id');
            }
        } catch (\Exception $e) {
            \Log::error('Error counting subscriptions: ' . $e->getMessage());
            $activeSubscriptions = 0;
        }

            $totalMeditations = Meditation::count();
            $premiumMeditations = Meditation::where('is_premium', true)->count();
            $freeMeditations = $totalMeditations - $premiumMeditations;

            $weeklyStats = collect([]);
            try {
                $weeklyStats = Meditation::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            } catch (\Exception $e) {
            }

            $categoryStats = Category::withCount('meditations')
                ->get()
                ->pluck('meditations_count', 'name');

            return view('admin.analytics.index', compact(
                'topMeditations',
                'meditationsByCategory',
                'totalUsers',
                'activeSubscriptions',
                'weeklyStats',
                'categoryStats',
                'totalMeditations',
                'premiumMeditations',
                'freeMeditations'
            ));
        }
}