<?php

namespace App\Http\Middleware;

use App\Models\CoreContext\Subscription;
use App\ValueObject\SubscriptionStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user()->toArray();
        $subscription = Subscription::query()
            ->where('company_id', $user['company_id'])
            ->get()
            ->first();

        if (!$subscription) {
            return response()->json(['status' => 'no_subscription_found'], 403);
        }

        if ($subscription->status !== SubscriptionStatus::ACTIVE) {
            return response()->json(['status' => 'no_active_subscription_found'], 403);
        }

        return $next($request);
    }
}
