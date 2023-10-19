<?php

namespace App\Http\Controllers\BillingContext;

use App\Models\BillingContext\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController
{
    public function create(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();
        $request->validate([
            'client_id' => 'string',
            'address' => 'string',
            'city' => 'string',
            'county' => 'string',
            'country' => 'string',
            'postcode' => 'string',
        ]);

        $requestArray = $request->toArray();
        $requestArray['company_id'] = $user['company_id'];
        $requestArray['resource_id'] = $requestArray['client_id'];
        $requestArray['type'] = 'CLIENT';
        Address::create($requestArray);
        return new JsonResponse();
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'address' => 'string',
            'city' => 'string',
            'county' => 'string',
            'country' => 'string',
            'postcode' => 'string',
        ]);

        $requestArray = $request->toArray();
        Address::find($id)->update($requestArray);
        return new JsonResponse();
    }

    public function delete()
    {}

    public function list()
    {
    }
}
