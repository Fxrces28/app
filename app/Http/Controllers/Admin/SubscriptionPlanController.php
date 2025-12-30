<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        try {
            $plans = SubscriptionPlan::orderBy('sort_order')->get();
        } catch (\Exception $e) {
            $plans = SubscriptionPlan::all();
        }
        
        return view('admin.subscriptions.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscriptions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|in:month,6months,year',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $durationDays = match($request->duration) {
            'month' => 30,
            '6months' => 180,
            'year' => 365,
            'lifetime' => 99999,
            default => 30
        };

        SubscriptionPlan::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'duration_days' => $durationDays,
            'features' => $request->features ? json_encode(explode("\n", $request->features)) : null,
            'is_active' => $request->has('is_active'),
            'is_popular' => $request->has('is_popular'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Тарифный план создан успешно!');
    }

    public function edit(SubscriptionPlan $subscription)
    {
        return view('admin.subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, SubscriptionPlan $subscription)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|in:month,6months,year',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $durationDays = match($request->duration) {
            'month' => 30,
            '6months' => 180,
            'year' => 365,
            default => 30
        };

        $subscription->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'duration_days' => $durationDays,
            'features' => $request->features ? json_encode(explode("\n", $request->features)) : null,
            'is_active' => $request->has('is_active'),
            'is_popular' => $request->has('is_popular'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Тарифный план обновлен успешно!');
    }

    public function destroy(Request $request, SubscriptionPlan $subscription)
    {
        try {
            $subscription->delete();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Тарифный план успешно удален'
                ]);
            }
            
            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Тарифный план удален успешно!');
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при удалении: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Ошибка при удалении: ' . $e->getMessage());
        }
    }
}