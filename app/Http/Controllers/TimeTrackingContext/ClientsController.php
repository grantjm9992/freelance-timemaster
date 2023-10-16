<?php declare(strict_types=1);

namespace App\Http\Controllers\TimeTrackingContext;

use App\Http\Controllers\Controller;
use App\Models\TimeTrackingContext\Clients;
use App\Models\TimeTrackingContext\Projects;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientsController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();
        $this->validate($request, [
            'name' => 'required|string',
            'description' => 'string|nullable',
        ]);

        $client = Clients::create([
            'name' => $request->name,
            'description' => $request->description,
            'company_id' => $user['company_id'],
            'active' => true,
        ]);

        return new JsonResponse([
            'message' => 'success',
            'data' => [
                'id' => $client->id
            ],
        ]);
    }

    public function find(string $id): JsonResponse
    {
        $client = Clients::find($id);

        return new JsonResponse([
            'message' => 'success',
            'data' => $client,
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $projects = Projects::query()
            ->where('client_id', $id)
            ->get()->toArray();
        if (count($projects) > 0) {
            throw new \Exception('Cannot delete clients with existing projects');
        }
        Clients::destroy([$id]);

        return new JsonResponse([
            'message' => 'success'
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|string',
            'description' => 'string|nullable',
            'active' => 'required|boolean',
        ]);

        $client = Clients::find($id);
        $client->update([
            'name' => $request->name,
            'description' => $request->description,
            'active' => $request->active,
        ]);

        return new JsonResponse([
            'message' => 'success',
        ]);
    }

    public function listAll(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();
        $clients = Clients::query()
            ->where('company_id', $user['company_id'])
            ->with('projects')
            ->orderBy('name');

        if ($request->query->get('name')) {
            $clients->where('name', 'LIKE', "%" . $request->query->get('name') . "%");
        }

        $clients = $clients->get()->toArray();

        return new JsonResponse([
            'message' => 'success',
            'data' => $clients,
        ]);
    }
}
