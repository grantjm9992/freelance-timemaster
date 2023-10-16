<?php

namespace App\Http\Middleware;

use App\Models\CoreContext\Subscription;
use App\ValueObject\SubscriptionType;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeTrackingContextMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user()->toArray();
        $subscription = Subscription::query()
            ->where('company_id', $user['company_id'])
            ->get()
            ->first();

        if (!in_array(SubscriptionType::TIME_TRACKING, $subscription->types)) {
            return response()->json(['status' => 'error_not_included_in_subscription'], 403);
        }

        return $next($request);
    }
}
