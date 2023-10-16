<?php

namespace App\Http\Controllers\AcademyContext;

use App\Http\Controllers\Controller;
use App\Models\AcademyContext\Course;
use App\Models\AcademyContext\CourseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CourseCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $entities = CourseCategory::query()
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

        CourseCategory::create([
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

        $entity = CourseCategory::find($id);
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
        $entity = CourseCategory::find($id);
        if ($entity === null) {
            throw new NotFoundHttpException();
        }

        $relatedEntities = Course::query()
            ->where('course_category_id', $id)
            ->get()->toArray();
        if (count($relatedEntities)) {
            throw new \Exception('Cannot delete course category assigned to active courses');
        }

        $entity->destroy();
        return response()->json();
    }
}
