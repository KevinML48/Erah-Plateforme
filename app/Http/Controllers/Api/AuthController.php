<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Auth\IssueApiTokenAction;
use App\Application\Actions\Auth\RegisterUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(
        RegisterRequest $request,
        RegisterUserAction $registerUserAction,
        IssueApiTokenAction $issueApiTokenAction
    ): JsonResponse {
        $validated = $request->validated();
        $user = $registerUserAction->execute(
            payload: $validated,
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        $token = $issueApiTokenAction->execute(
            user: $user,
            deviceName: $validated['device_name'],
            reason: 'register',
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'me_endpoint' => '/api/me',
            'user' => $user,
        ], 201);
    }

    public function login(
        LoginRequest $request,
        IssueApiTokenAction $issueApiTokenAction
    ): JsonResponse {
        $validated = $request->validated();
        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants invalides.'],
            ]);
        }

        $token = $issueApiTokenAction->execute(
            user: $user,
            deviceName: $validated['device_name'],
            reason: 'password-login',
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'me_endpoint' => '/api/me',
            'user' => $user,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request, StoreAuditLogAction $storeAuditLogAction): JsonResponse
    {
        $user = $request->user();
        $currentToken = $user?->currentAccessToken();

        DB::transaction(function () use ($storeAuditLogAction, $user, $currentToken, $request) {
            $currentToken?->delete();

            if ($user) {
                $storeAuditLogAction->execute(
                    action: 'auth.logout',
                    actor: $user,
                    target: $user,
                    context: [
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ],
                );
            }
        });

        return response()->json([
            'message' => 'Logged out.',
        ]);
    }
}
