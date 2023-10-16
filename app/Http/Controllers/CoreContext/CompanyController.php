<?php declare(strict_types=1);

namespace App\Http\Controllers\CoreContext;

use App\Http\Controllers\Controller;
use App\Models\CoreContext\Company;
use App\Models\TimeTrackingContext\CheckApprovers;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $companies = Company::all();

        return response()->json([
            'status' => 'success',
            'data' => $companies,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $company = Company::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Company created successfully',
            'data' => $company,
        ]);
    }

    public function show(string $id)
    {
        $company = Company::find($id)
            ->with('clients.projects')
            ->with('employees')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $company,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $company = Company::find($id);
        if (null === $company) {
            throw new NotFoundHttpException('Company not found');
        }
        $company->update($request->toArray());
        $company->save();

        return response()->json([
            'status' => 'success',
            'data' => $company,
        ]);
    }

    public function destroy(string $id)
    {
        Company::destroy($id);
    }
}
