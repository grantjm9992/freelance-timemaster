<?php

namespace App\Http\Controllers\TimeTrackingContext;

use App\Http\Controllers\Controller;
use App\Models\CoreContext\User;
use App\Models\TimeTrackingContext\Check;
use App\ValueObject\CheckStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WhosInController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user()->toArray();
        $users = User::query()
            ->where('company_id', $user['company_id'])
            ->get()->toArray();
        $activeChecks = Check::query()
            ->select('user_id')
            ->where('company_id', $user['company_id'])
            ->where('status', CheckStatus::open())
            ->get()->toArray();
        $activeUserIds = [];
        foreach ($activeChecks as $check) {
            $activeUserIds[] = $check['user_id'];
        }
        $activeUsers = array_filter($users, function ($user) use ($activeUserIds) {
            return in_array($user['id'], $activeUserIds);
        });
        $inactiveUsers = array_filter($users, function ($user) use ($activeUserIds) {
            return !in_array($user['id'], $activeUserIds);
        });

        return response()->json(['data' => [
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
        ]]);
    }
}
