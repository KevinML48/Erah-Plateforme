<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\GuidedTour\PlatformGuidedTourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuidedTourController extends Controller
{
    public function __construct(
        private readonly PlatformGuidedTourService $platformGuidedTourService,
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->platformGuidedTourService->show($request->user()),
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->platformGuidedTourService->start($request->user()),
        ]);
    }

    public function restart(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->platformGuidedTourService->restart($request->user()),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'string', Rule::in(['next', 'previous', 'pause', 'resume'])],
        ]);

        return response()->json([
            'data' => $this->platformGuidedTourService->update(
                $request->user(),
                (string) $validated['action'],
            ),
        ]);
    }
}
