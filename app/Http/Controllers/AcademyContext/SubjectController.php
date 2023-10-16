<?php

namespace App\Http\Controllers\AcademyContext;

use App\Http\Controllers\Controller;
use App\Models\AcademyContext\Course;
use App\Models\AcademyContext\CourseCategory;
use App\Models\AcademyContext\Group;
use App\Models\AcademyContext\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $entities = Subject::query()
            ->where('company_id')
            ->get()
            ->all();

        return response()->json([
            'data' => $entities,
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();
        $request->validate([
            'name' => 'string|required',
        ]);

        Subject::create([
            'name' => $request->name,
            'company_id' => $user['company_id'],
        ]);

        return response()->json();
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => 'string|required',
        ]);

        $entity = Subject::find($id);
        if ($entity === null) {
            throw new NotFoundHttpException();
        }

        $entity->update([
            'name' => $request->name,
        ]);

        return response()->json();
    }

    public function delete(string $id): JsonResponse
    {
        $entity = Subject::find($id);
        if ($entity === null) {
            throw new NotFoundHttpException();
        }

        $relatedEntities = Group::query()
            ->where('subject_id', $id)
            ->get()->toArray();
        if (count($relatedEntities)) {
            throw new \Exception('Cannot delete subject assigned to active groupd');
        }

        $entity->destroy();
        return response()->json();
    }
}
