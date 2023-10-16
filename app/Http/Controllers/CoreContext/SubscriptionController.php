<?php

namespace App\Http\Controllers\CoreContext;

use App\Http\Controllers\Controller;
use App\Models\CoreContext\Subscription;
use App\Models\CoreContext\User;
use App\ValueObject\SubscriptionStatus;
use App\ValueObject\SubscriptionType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ReflectionClass;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $request->validate([
            'number_of_users' => 'required|integer',
            'types' => 'required|array',
            'stripe_token' => 'required|string',
            'type' => 'required|string',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentMethod = PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'token' => $request->stripe_token,
            ]
        ]);
        if (!$user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }
        $user->updateDefaultPaymentMethod($paymentMethod);

        $user->newSubscription('Monthly', 'price_1NTNgtIE24BO5pGyDq8DZ8gy')->create($user->defaultPaymentMethod()->asStripePaymentMethod());

        $types = [];
        $availableTypes = (new ReflectionClass(SubscriptionType::class))->getConstants();
        foreach ($request->types as $type) {
            if (in_array($type, $availableTypes)) {
                $types[] = $type;
            }
        }
        $today = Carbon::now();
        Subscription::create([
            'company_id' => $user['company_id'],
            'types' => $types,
            'start_date' => $today->format('Y-m-d'),
            'next_renew_date' => $today->addMonth()->format('Y-m-d'),
            'status' => SubscriptionStatus::ACTIVE,
            'number_of_users' => $request->number_of_users,
        ]);

        return response()->json([]);
    }

    public function getSubscription()
    {
        /** @var User $user */
        $user = Auth::user();
        $subscriptions = Subscription::query()
            ->where('company_id', $user->getAttribute('company_id'))
            ->where('status', 'active')
            ->first()->toArray();

        return response()->json(['data' => $subscriptions]);
    }
    private static function getPrice(int $users, string $type): int
    {
        $priceArray = [
            10 => [
                'advanced' => 30,
                'basic' => 20,
            ],
            20 => [
                'advanced' => 60,
                'basic' => 40,
            ],
            50 => [
                'advanced' => 150,
                'basic' => 100,
            ],
        ];

        return $priceArray[$users][$type] ?? 100;
    }

    public function getStripeToken(): JsonResponse
    {
        return response()->json([
            'token' => env('STRIPE_SECRET'),
        ]);
    }
}
