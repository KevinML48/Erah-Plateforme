<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request, PushNotificationService $pushNotificationService): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'max:4000'],
            'public_key' => ['required', 'string', 'max:2000'],
            'auth_token' => ['required', 'string', 'max:2000'],
            'content_encoding' => ['nullable', 'string', 'max:30'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', 'max:50'],
        ]);

        $subscription = $pushNotificationService->subscribe($request->user(), $validated);

        return response()->json([
            'data' => [
                'id' => $subscription->id,
                'endpoint_hash' => $subscription->endpoint_hash,
                'is_active' => $subscription->is_active,
            ],
        ], 201);
    }

    public function destroy(Request $request, PushNotificationService $pushNotificationService): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'max:4000'],
        ]);

        $pushNotificationService->unsubscribe($request->user(), $validated['endpoint']);

        return response()->json(['message' => 'Push subscription disabled.']);
    }
}
