<?php

namespace App\Http\Controllers\Web\Assistant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Assistant\UpdateAssistantConversationRequest;
use App\Models\AssistantConversation;
use App\Services\AI\AssistantConsolePageService;
use App\Services\AI\AssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AssistantConversationController extends Controller
{
    public function __construct(
        private readonly AssistantService $assistantService,
        private readonly AssistantConsolePageService $assistantConsolePageService,
    ) {
    }

    public function update(UpdateAssistantConversationRequest $request, AssistantConversation $conversation): JsonResponse
    {
        $ownedConversation = $this->resolveOwnedConversation($request, $conversation);

        $updatedConversation = $this->assistantService->renameConversation(
            user: $request->user(),
            conversationId: $ownedConversation->id,
            title: $request->string('title')->toString(),
        );

        return response()->json([
            'data' => [
                'conversation' => $this->assistantConsolePageService->mapConversationForRealtime($updatedConversation),
            ],
        ]);
    }

    public function destroy(Request $request, AssistantConversation $conversation): Response
    {
        $ownedConversation = $this->resolveOwnedConversation($request, $conversation);

        $this->assistantService->deleteConversation($request->user(), $ownedConversation->id);

        return response()->noContent();
    }

    private function resolveOwnedConversation(Request $request, AssistantConversation $conversation): AssistantConversation
    {
        return AssistantConversation::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($conversation->id);
    }
}
