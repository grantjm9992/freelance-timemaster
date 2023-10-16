<?php

namespace App\Http\Middleware;

use App\ValueObject\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user()->toArray();
        if ($user['user_role'] !== UserRole::companyAdmin()) {
            return response()->json(['status' => 'user_not_authorised'], 403);
        }

        return $next($request);
    }
}
