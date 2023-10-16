<?php

use App\Http\Controllers\CoreContext\AuthController;
use App\Http\Controllers\CoreContext\CompanyController;
use App\Http\Controllers\CoreContext\RoleController;
use App\Http\Controllers\CoreContext\SubscriptionController;
use App\Http\Controllers\CoreContext\UsersController;
use App\Http\Controllers\TimeTrackingContext\CheckApproversController;
use App\Http\Controllers\TimeTrackingContext\CheckController;
use App\Http\Controllers\TimeTrackingContext\CheckReportController;
use App\Http\Controllers\TimeTrackingContext\ClientsController;
use App\Http\Controllers\TimeTrackingContext\ProjectsController;
use App\Http\Controllers\TimeTrackingContext\TasksController;
use App\Http\Controllers\TimeTrackingContext\WhosInController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('confirm-email', 'validateEmail');
    Route::post('set-password', 'setPassword');
});

Route::middleware('jwt.verify')->group(function() {

    Route::controller(CompanyController::class)->prefix('companies/')->group(function() {
        Route::get('', 'index');
        Route::get('{id}', 'show');
        Route::delete('{id}', 'delete');
        Route::post('{id}', 'update');
    });
    Route::controller(SubscriptionController::class)->prefix('subscription/')->group(function() {
        Route::post('', 'create');
        Route::get('', 'getSubscription');
        Route::get('stripe-token', 'getStripeToken');
    });

    Route::middleware('subscription')->group(function () {

        Route::controller(UsersController::class)->prefix('users/')->group(function() {
            Route::get('', 'listAll');
            Route::post('', 'create');
            Route::get('{id}', 'find');
            Route::delete('{id}', 'delete');
            Route::post('{id}', 'update');
            Route::post('update-password/{id}', 'updatePassword');
        });

        Route::controller(RoleController::class)->prefix('roles/')->group(function() {
            Route::post('', 'create');
            Route::get('', 'index');
        });

        // TimeTrackingContext
        Route::middleware('time.tracking')->group(function() {

            Route::controller( ClientsController::class)->prefix('clients/')->group(function () {
                Route::post('', 'create');
                Route::get('', 'listAll');
                Route::get('{id}', 'find');
                Route::delete('{id}', 'delete');
                Route::post('{id}', 'update');
            });

            Route::controller(ProjectsController::class)->prefix('projects/')->group(function() {
                Route::get('jira-projects', 'getJiraProjects');
                Route::get('', 'listAll');
                Route::post('', 'create');
                Route::get('{id}', 'find');
                Route::delete('{id}', 'delete');
                Route::post('{id}', 'update');
            });

            Route::controller(TasksController::class)->prefix('tasks/')->group(function() {
                Route::post('', 'create');
                Route::get('', 'index');
                Route::post('{id}', 'update');
                Route::get('{id}', 'show');
                Route::delete('{id}', 'delete');
            });

            Route::controller(CheckController::class)->prefix('checks/')->group(function() {
                Route::post('', 'create');
                Route::post('check-in', 'checkIn');
                Route::post('check-out', 'checkOut');
                Route::get('', 'index');
                Route::post('/user-calendar/{userId}', 'getCalendarForUser');
                Route::post('/team-calendar', 'getCalendarForTeam');
                Route::get('{id}', 'show');
                Route::post('{id}', 'update');
                Route::delete('{id}', 'delete');
            });

            Route::controller(CheckApproversController::class)->prefix('check-approvers/')->group(function() {
                Route::get('', 'index');
                Route::post('{companyId}', 'updateCheckApprovers');
            });

            Route::controller(CheckReportController::class)->prefix('check-report/')->group(function() {
                Route::get('', 'hoursWorked');
                Route::get('user', 'hoursByEmployee');
                Route::get('task', 'hoursByTask');
                Route::get('project', 'hoursByProject');
                Route::get('client', 'hoursByClient');
            });

            Route::controller(WhosInController::class)->prefix('whos-in/')->group(function () {
                Route::get('', 'index');
            });
        });
    });
});
