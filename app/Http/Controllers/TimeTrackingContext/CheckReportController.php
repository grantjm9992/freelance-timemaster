<?php

namespace App\Http\Controllers\TimeTrackingContext;

use App\Http\Controllers\Controller;
use App\Models\TimeTrackingContext\Check;
use App\ValueObject\CheckStatus;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckReportController extends Controller
{
    public function hoursWorked(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();

        $checks = Check::query()
            ->where('user_id', $user['id'])
            ->where('status', CheckStatus::closed())
            ->where(
                'date_started',
                '>=',
                Carbon::now()
                    ->startOfWeek(1)
                    ->subDays(30)
                    ->setTime(0, 0, 0)
                    ->format('Y-m-d H:i:s')
            )->where(
                'date_started',
                '<',
                Carbon::now()
                    ->addWeek()
                    ->startOfWeek(1)
                    ->format('Y-m-d H:i:s')
            )->get()->toArray();

        $lastWeek = array_filter($checks, function ($check) {
            return $check['date_started'] <
                Carbon::now()
                    ->startOfWeek(1)
                    ->setTime(0, 0, 0)
                    ->format('Y-m-d H:i:s') && $check['date_started'] >=
                Carbon::now()
                    ->startOfWeek(1)
                    ->setTime(0, 0, 0);
        });
        $thisWeek = array_filter($checks, function ($check) {
            return $check['date_started'] >=
                Carbon::now()
                    ->startOfWeek(1)
                    ->setTime(0, 0, 0)
                    ->format('Y-m-d H:i:s');
        });

        return response()->json([
            'thisWeek' => $this->getTotalTimeForArrayOfChecks($thisWeek),
            'lastWeek' => $this->getTotalTimeForArrayOfChecks($lastWeek),
            'lastThirtyDays' => $this->getTotalTimeForArrayOfChecks($checks)
        ]);
    }

    private function getTotalTimeForArrayOfChecks(array $checks): int
    {
        $total = 0;
        foreach ($checks as $check) {
            $total += (int)Carbon::parse($check['date_ended'])->format('U') - (int)Carbon::parse($check['date_started'])->format('U');
        }
        return $total;
    }

    public function hoursByEmployee(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();
        $checks = Check::query()
            ->select('checks.date_started', 'checks.date_ended', 'users.name', 'users.surname', 'checks.user_id')
            ->leftJoin('users', 'users.id', '=', 'checks.user_id')
            ->where('checks.date_started', '>=', Carbon::parse($request->from)->format('Y-m-d 00:00:00'))
            ->where('checks.date_started', '<=', Carbon::parse($request->to)->format('Y-m-d 23:59:59'))
            ->where('checks.company_id', $user['company_id'])
            ->get()->toArray();
        $returnArray = [];
        $toggleArray = [];
        foreach ($checks as $check) {
            $key = $check['user_id'];
            if (array_key_exists($key, $toggleArray)) {
                $toggleArray[$key]['time'] += floor(($this->getTotalTimeForArrayOfChecks([$check])/3600));
            } else {
                $toggleArray[$key] = [
                    'name' => $check['name'],
                    'surname' => $check['surname'],
                    'time' => floor(($this->getTotalTimeForArrayOfChecks([$check])/3600)),
                ];
            }
        }

        foreach ($toggleArray as $key => $value) {
            $returnArray[] = $value;
        }

        return response()->json($returnArray);
    }

    public function hoursByClient(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();
        $checks = Check::query()
            ->select('checks.date_started', 'checks.date_ended', 'clients.name', 'projects.client_id')
            ->leftJoin('tasks', 'tasks.id', '=', 'checks.task_id')
            ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->leftJoin('clients', 'clients.id', '=', 'projects.client_id')
            ->where('checks.date_started', '>=', Carbon::parse($request->from)->format('Y-m-d 00:00:00'))
            ->where('checks.date_started', '<=', Carbon::parse($request->to)->format('Y-m-d 23:59:59'))
            ->where('checks.company_id', $user['company_id'])
            ->get()->toArray();
        $returnArray = [];
        $toggleArray = [];
        foreach ($checks as $check) {
            $key = $check['client_id'];
            if (array_key_exists($key, $toggleArray)) {
                $toggleArray[$key]['time'] += floor(($this->getTotalTimeForArrayOfChecks([$check])/3600));
            } else {
                $toggleArray[$key] = [
                    'name' => $check['name'] ?? 'No client',
                    'time' => floor(($this->getTotalTimeForArrayOfChecks([$check])/3600)),
                ];
            }
        }

        foreach ($toggleArray as $key => $value) {
            $returnArray[] = $value;
        }

        return response()->json($returnArray);
    }

    public function hoursByProject(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();
        $checks = Check::query()
            ->select('checks.date_started', 'checks.date_ended', 'projects.name', 'tasks.project_id')
            ->leftJoin('tasks', 'tasks.id', '=', 'checks.task_id')
            ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->where('checks.date_started', '>=', Carbon::parse($request->from)->format('Y-m-d 00:00:00'))
            ->where('checks.date_started', '<=', Carbon::parse($request->to)->format('Y-m-d 23:59:59'))
            ->where('checks.company_id', $user['company_id'])
            ->get()->toArray();
        $returnArray = [];
        $toggleArray = [];
        foreach ($checks as $check) {
            $key = $check['project_id'];
            if (array_key_exists($key, $toggleArray)) {
                $toggleArray[$key]['time'] += floor(($this->getTotalTimeForArrayOfChecks([$check])/3600));
            } else {
                $toggleArray[$key] = [
                    'name' => $check['name'] ?? 'No project',
                    'time' => floor(($this->getTotalTimeForArrayOfChecks([$check])/3600)),
                ];
            }
        }

        foreach ($toggleArray as $key => $value) {
            $returnArray[] = $value;
        }

        return response()->json($returnArray);
    }

    public function hoursByTask(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();
        $checks = Check::query()
            ->select('checks.date_started', 'checks.date_ended', 'tasks.name', 'checks.task_id')
            ->leftJoin('tasks', 'tasks.id', '=', 'checks.task_id')
            ->where('checks.date_started', '>=', Carbon::parse($request->from)->format('Y-m-d 00:00:00'))
            ->where('checks.date_started', '<=', Carbon::parse($request->to)->format('Y-m-d 23:59:59'))
            ->where('checks.company_id', $user['company_id'])
            ->get()->toArray();
        $returnArray = [];
        $toggleArray = [];
        foreach ($checks as $check) {
            $key = $check['task_id'];
            if (array_key_exists($key, $toggleArray)) {
                $toggleArray[$key]['time'] += floor(($this->getTotalTimeForArrayOfChecks([$check])/3600));
            } else {
                $toggleArray[$key] = [
                    'name' => $check['name'] ?? 'No task',
                    'time' => floor(($this->getTotalTimeForArrayOfChecks([$check])/3600)),
                ];
            }
        }

        foreach ($toggleArray as $key => $value) {
            $returnArray[] = $value;
        }

        return response()->json($returnArray);
    }
}

