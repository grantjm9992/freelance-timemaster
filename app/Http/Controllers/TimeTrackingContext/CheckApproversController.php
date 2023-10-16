<?php

namespace App\Http\Controllers\TimeTrackingContext;

use App\Models\CoreContext\User;
use App\Models\TimeTrackingContext\CheckApprovers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApproversController
{
    public function index(): JsonResponse
    {
        $user = Auth::user()->toArray();
        $users = User::query()
            ->where('company_id', $user['company_id'])
            ->where('user_role', 'user')
            ->get()->toArray();
        $checkApprovers = CheckApprovers::query()
            ->where('company_id', $user['company_id'])
            ->get()->all();
        $checkApproverIds = [];
        foreach ($checkApprovers as $approver) {
            $checkApproverIds[] = $approver->user_id;
        }
        $usersToReturn = [];
        foreach ($users as $_user) {
            $_user['is_approver'] = in_array($_user['id'], $checkApproverIds);
            $usersToReturn[] = $_user;
        }

        return response()->json(['data' => $usersToReturn]);
    }

    public function updateCheckApprovers(Request $request, string $companyId): JsonResponse
    {
        CheckApprovers::query()
            ->where('company_id', $companyId)
            ->delete();
        foreach ($request->approvers as $approver) {
            CheckApprovers::create([
                'company_id' => $companyId,
                'user_id' => $approver,
            ]);
        }

        return response()->json([]);
    }
}
