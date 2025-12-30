<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class PublicSubscriptionController extends Controller
{
    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
            
        return view('subscriptions.plans', compact('plans'));
    }
}