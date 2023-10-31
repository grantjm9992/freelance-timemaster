<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\TimeTrackingContext\TasksController;
use App\Models\CoreContext\Company;
use App\Models\CoreContext\User;
use App\Models\TimeTrackingContext\Clients;
use App\Models\TimeTrackingContext\Projects;
use App\Models\TimeTrackingContext\Tasks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\TextUI\XmlConfiguration\Group;
use Tests\CreatesApplication;
use Tests\TestCase;

#[Group(name: 'feature')]
class TasksControllerTest extends TestCase
{
    use CreatesApplication;
    private Company $company;
    private Clients $client;
    private Projects $project;
    private User $user;
    private string $id;

    public function testCreateTask()
    {

        $this->company = Company::create(['name' => 'company']);
        $this->client = Clients::create([
            'name' => 'client',
            'company_id' => $this->company->id,
            'active' => true,
        ]);
        $this->project = Projects::create([
            'name' => 'project',
            'client_id' => $this->client->id,
            'company_id' => $this->company->id,
            'active' => true,
        ]);
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('toArray')
            ->willReturn([
                'id' => 'id',
                'company_id' => $this->company->id,
            ]);
        Auth::shouldReceive('user')->andReturn($user);

        $request = Request::create('api/tasks', 'POST', [
            'project_id' => $this->project->id,
            'name' => 'Your Task Name',
            'description' => 'Task Description',
        ]);

        $controller = new TasksController();

        $response = $controller->create($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('success', $data['message']);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('id', $data['data']);
        $this->id = $data['data']['id'];

        $task = Tasks::where('name', 'Your Task Name')->first();
        $this->assertNotNull($task);
    }

    public function testFindTask(): void
    {

        $this->company = Company::create(['name' => 'company']);
        $this->client = Clients::create([
            'name' => 'client',
            'company_id' => $this->company->id,
            'active' => true,
        ]);
        $this->project = Projects::create([
            'name' => 'project',
            'client_id' => $this->client->id,
            'company_id' => $this->company->id,
            'active' => true,
        ]);
        $task = Tasks::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'client_id' => $this->client->id,
            'name' => 'Your Task Name',
            'description' => 'Task Description',
        ]);
        $user = $this->createMock(User::class);
        $user->method('toArray')
            ->willReturn([
                'id' => 'id',
                'company_id' => $this->company->id,
            ]);
        Auth::shouldReceive('user')->andReturn($user);
        $controller = new TasksController();

        $response = $controller->show($task->id);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('success', $data['message']);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('id', $data['data']);
    }
}