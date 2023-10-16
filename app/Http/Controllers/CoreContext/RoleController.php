<?php declare(strict_types=1);

namespace App\Http\Controllers\CoreContext;

use App\Http\Controllers\Controller;
use App\Models\CoreContext\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user()->toArray();

        $roles = Role::query()
            ->where('company_id', $user['company_id'])
            ->get()
            ->all();

        return new JsonResponse([
            'message' => 'success',
            'data' => $roles,
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);

        $user = Auth::user()->toArray();

        Role::create([
            'company_id' => $user['company_id'],
            'name' => $request->name
        ]);

        return new JsonResponse([
            'message' => 'success',
        ]);
    }
}
