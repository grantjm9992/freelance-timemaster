<?php

namespace App\Http\Controllers\BillingContext;

use App\Models\BillingContext\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController
{
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'string',
            'project_id' => 'string',
            'task_id' => 'string',
            'status' => 'string',
            'recipient' => 'string',
            'payer' => 'string',
            'items' => 'array',
            'total' => 'string',
            'currency' => 'string',
            'tax_rate' => 'string',
            'tax_applied' => 'string',
            'total_including_tax' => 'string',
            'create_date' => 'string',
            'due_date' => 'string',
            'paid_date' => 'string',
            'amount_paid' => 'string',
            'title' => 'string',
            'description' => 'string',
        ]);

        $user = Auth::user()->toArray();
        $requestArray = $request->toArray();
        $requestArray['company_id'] = $user['company_id'];
        $requestArray['user_id'] = $user['id'];
        $requestArray['recipient'] = $user['id'];
        Invoice::create($requestArray);
        return new JsonResponse();
    }

    public function update()
    {}

    public function updateStatus()
    {}

    public function delete()
    {}

    public function list(): JsonResponse
    {
        $user = Auth::user()->toArray();
        $objects = Invoice::query()
            ->where('company_id', $user['company_id'])
            ->get()
            ->all();
        return new JsonResponse([
            'data' => $objects,
        ]);
    }
}