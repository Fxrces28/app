<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if ($user->isAdmin()) {
            return $next($request);
        }

        if (!$user->canAccessPremiumContent()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Требуется подписка для доступа к этому контенту'
                ], 403);
            }
            

            return redirect()->route('subscriptions.plans')
                ->with('error', 'Для доступа к этой медитации требуется активная подписка');
        }
        
        return $next($request);
    }
}