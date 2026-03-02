<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Notifications\RegisterUserDeviceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterUserDeviceRequest;
use Illuminate\Http\JsonResponse;

class UserDeviceController extends Controller
{
    public function store(
        RegisterUserDeviceRequest $request,
        RegisterUserDeviceAction $registerUserDeviceAction
    ): JsonResponse {
        $device = $registerUserDeviceAction->execute($request->user(), $request->validated());

        return response()->json([
            'data' => $device,
        ], $device->wasRecentlyCreated ? 201 : 200);
    }
}
