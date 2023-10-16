<?php declare(strict_types=1);

namespace App\Http\Controllers\CoreContext;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmRegistration;
use App\Models\CoreContext\Company;
use App\Models\CoreContext\Subscription;
use App\Models\CoreContext\User;
use App\Models\TimeTrackingContext\Check;
use App\Models\TimeTrackingContext\CheckApprovers;
use App\ValueObject\CheckStatus;
use App\ValueObject\SubscriptionStatus;
use App\ValueObject\SubscriptionType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use ReflectionClass;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => ['login', 'register', 'validateEmail', 'setPassword'],
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');
        $credentials['email_confirmed'] = 1;

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user()->toArray();
        $company = Company::query()->where('id', $user['company_id'])->with('subscription')->get()->first();

        $check = Check::query()
            ->where('user_id', $user['id'])
            ->where('status', CheckStatus::open())
            ->first();

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'check' => $check,
            'company' => $company,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'company_name' => 'required|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user !== null) {
            throw new \Exception('User already exists');
        }

        $company = Company::create([
            'name' => $request->company_name,
            'number_of_employees' => $request->number_of_employees ?? 0,
        ]);

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'user_role' => 'company_admin',
        ]);

        $company->admin_user_id = $user->id;
        $company->save();

        $token = Auth::login($user);

        Mail::to($user->email)->send(new ConfirmRegistration(base64_encode($user->id)));

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ]);
    }

    public function setPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string',
        ]);

        $id = base64_decode($request->token);

        /** @var User $user */
        $user = User::find($id);
        if (null === $user) {
            throw new \Exception('User not found');
        }

        $user->setAttribute('email_confirmed', 1);
        $user->setAttribute('password', Hash::make($request->password));
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully updated',
        ]);
    }

    public function validateEmail(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $id = base64_decode($request->token);

        /** @var User $user */
        $user = User::find($id);
        if (null === $user) {
            throw new \Exception('User not found');
        }

        $user->setAttribute('email_confirmed', 1);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully updated',
        ]);
    }

    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh(): JsonResponse
    {
        $user = Auth::user();
        $check = Check::query()
            ->where('user_id', $user['id'])
            ->where('status', CheckStatus::open())
            ->first();
        $company = Company::query()->where('id', $user['company_id'])->with('subscription')->get()->first();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'check' => $check,
            'company' => $company,
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ],
        ]);
    }
}
