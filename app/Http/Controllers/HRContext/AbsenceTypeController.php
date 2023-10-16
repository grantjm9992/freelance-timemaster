<?php

namespace App\Http\Controllers\HRContext;

use App\Http\Controllers\Controller;
use App\Models\HRContext\AbsenceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user()->toArray();
        $absenceTypes = AbsenceType::query()
            ->where('company_id', $user['company_id'])
            ->get()
            ->all();

        return response()->json(['data' => $absenceTypes]);
    }

    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $absenceType = AbsenceType::create($request->toArray());

        return response()->json(['data' => $absenceType]);
    }

    public function delete(string $id): JsonResponse
    {
        AbsenceType::destroy($id);

        return response()->json([]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $absenceType = AbsenceType::find($id);
        $absenceType->update($request->toArray());

        return response()->json([]);
    }
}
