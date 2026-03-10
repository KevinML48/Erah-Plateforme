<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\AskHelpAssistantRequest;
use App\Services\HelpAssistantService;
use Illuminate\Http\JsonResponse;

class HelpAssistantController extends Controller
{
    public function __construct(
        private readonly HelpAssistantService $helpAssistantService,
    ) {
    }

    public function __invoke(AskHelpAssistantRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->helpAssistantService->ask(
                message: $request->string('message')->toString(),
                user: $request->user(),
            ),
        ]);
    }
}
