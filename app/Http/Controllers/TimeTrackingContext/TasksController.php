<?php declare(strict_types=1);

namespace App\Http\Controllers\TimeTrackingContext;

use App\Http\Controllers\Controller;
use App\Models\TimeTrackingContext\Check;
use App\Models\TimeTrackingContext\Tasks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();
        $tasks = Tasks::query()
            ->where('company_id', $user['company_id'])
            ->get()
            ->all();

        return new JsonResponse([
            'message' => 'success',
            'data' => $tasks
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'project_id' => 'required|string',
            'name' => 'required|string',
            'description' => 'string|nullable',
            'assigned_to' => 'string|nullable',
        ]);

        $user = Auth::user()->toArray();

        $task = Tasks::create([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'company_id' => $user['company_id'],
        ]);

        return new JsonResponse([
            'message' => 'success',
            'data' => [
                'id' => $task->id
            ]
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $task = Tasks::find($id)
            ->with('assignedTo')
            ->first();

        return new JsonResponse([
            'message' => 'success',
            'data' => $task,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $this->validate($request, [
            'project_id' => 'required|string',
            'name' => 'required|string',
            'description' => 'string|nullable',
            'assigned_to' => 'string|nullable',
        ]);

        $task = Tasks::find($id);
        $task->update([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
        ]);

        return new JsonResponse([
            'message' => 'success'
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $checks = Check::query()
            ->where('task_id', $id)
            ->get()->toArray();
        if (count($checks) > 0) {
            throw new \Exception('Cannot delete tasks with existing checks');
        }
        Tasks::destroy($id);

        return new JsonResponse([
            'message' => 'success'
        ]);
    }
}
