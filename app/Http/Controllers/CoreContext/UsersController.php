<?php declare(strict_types=1);

namespace App\Http\Controllers\CoreContext;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmRegistration;
use App\Mail\UserCreatedEmail;
use App\Models\CoreContext\User;
use App\Models\TimeTrackingContext\Clients;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'surname' => 'required|string',
            'user_role' => 'required|string',
            'role_id' => 'string|nullable',
        ]);

        $dataArray = $request->toArray();
        $dataArray['company_id'] = $user['company_id'];

        $user = User::create($dataArray);
        Mail::to($user->email)->send(new UserCreatedEmail(base64_encode($user->id)));

        return new JsonResponse([
            'message' => 'success',
            'data' => [
                'id' => $user->id
            ],
        ]);
    }

    public function find(string $id): JsonResponse
    {
        $client = User::find($id);

        return new JsonResponse([
            'message' => 'success',
            'data' => $client,
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $user = User::find($id);
        $user->delete();

        return new JsonResponse([
            'message' => 'success'
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|string',
            'surname' => 'required|string',
            'user_role' => 'required|string',
            'role_id' => 'string|nullable',
        ]);

        $client = User::find($id);
        $client->update($request->toArray());

        return new JsonResponse([
            'message' => 'success',
        ]);
    }

    public function updatePassword(Request $request, string $id): JsonResponse
    {
        $this->validate($request, [
            'password' => 'required|string',
        ]);

        $user = User::find($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return new JsonResponse([
            'message' => 'success',
        ]);
    }

    public function listAll(Request $request): JsonResponse
    {
        $user = Auth::user()->toArray();
        $clients = User::query()
            ->where('company_id', $user['company_id'])
            ->where('deleted_at', '=', null)
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
