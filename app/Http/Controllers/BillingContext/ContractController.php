<?php

namespace App\Http\Controllers\BillingContext;

use App\Models\BillingContext\Contract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController
{
    private array $user;

    public function __construct()
    {
        $this->user = Auth::user()->toArray();
    }

    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'client_id',
            'title',
            'pay_amount',
            'rate_of_pay',
            'period_of_pay',
            'currency',
            'start_date',
            'end_date',
            'status',
        ]);

        $requestArray = $request->toArray();
        $requestArray['user_id'] = $this->user['id'];
        $requestArray['company_id'] = $this->user['company_id'];
        Contract::create($requestArray);
        return response()->json();
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'client_id',
            'title',
            'pay_amount',
            'rate_of_pay',
            'period_of_pay',
            'currency',
            'start_date',
            'end_date',
            'status',
        ]);

        $requestArray = $request->toArray();
        Contract::find($id)->update($requestArray);
        return response()->json();
    }

    public function list(Request $request): JsonResponse
    {
        $clientId = $request->query->get('client_id');
        $projectId = $request->query->get('project_id');
        $query = Contract::query()
            ->where('company_id', $this->user['company_id']);

        if ($clientId) {
            $query->where('client_id', $clientId);
        }
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $objects = $query
            ->get()
            ->all();
        return response()->json([
            'data' => $objects,
        ]);
    }

    public function find(string $id): JsonResponse
    {
        $object = Contract::find($id);
        return response()->json(['data' => $object]);
    }

    public function delete(string $id): JsonResponse
    {
        Contract::destroy($id);
        return response()->json();
    }
}
