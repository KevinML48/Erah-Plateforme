<?php

namespace App\Http\Controllers\Web\Assistant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Assistant\StoreAssistantMessageRequest;
use App\Models\AssistantConversation;
use App\Services\AI\AssistantConsolePageService;
use App\Services\AI\AssistantService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AssistantMessageController extends Controller
{
    public function __construct(
        private readonly AssistantService $assistantService,
        private readonly AssistantConsolePageService $assistantConsolePageService,
    ) {
    }

    public function store(StoreAssistantMessageRequest $request): JsonResponse
    {
        $this->guardConversationOwnership($request);

        try {
            $payload = $this->assistantService->send(
                user: $request->user(),
                message: $request->string('message')->toString(),
                conversationId: $request->integer('conversation_id') ?: null,
            );
        } catch (ModelNotFoundException $exception) {
            throw $exception;
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json([
            'data' => [
                'conversation' => $this->assistantConsolePageService->mapConversationForRealtime($payload['conversation']),
                'user_message' => $this->assistantConsolePageService->mapMessage($payload['user_message']),
                'assistant_message' => $this->assistantConsolePageService->mapMessage($payload['assistant_message']),
            ],
        ]);
    }

    public function stream(StoreAssistantMessageRequest $request): StreamedResponse|JsonResponse
    {
        $this->guardConversationOwnership($request);

        try {
            return response()->stream(function () use ($request): void {
                $this->sendSse('ready', ['ok' => true]);

                try {
                    $payload = $this->assistantService->stream(
                        user: $request->user(),
                        message: $request->string('message')->toString(),
                        conversationId: $request->integer('conversation_id') ?: null,
                        onDelta: function (string $delta): void {
                            $this->sendSse('delta', ['delta' => $delta]);
                        },
                    );
                } catch (Throwable $exception) {
                    $this->sendSse('error', [
                        'message' => $exception->getMessage(),
                    ]);

                    return;
                }

                $this->sendSse('conversation', [
                    'conversation' => $this->assistantConsolePageService->mapConversationForRealtime($payload['conversation']),
                    'user_message' => $this->assistantConsolePageService->mapMessage($payload['user_message']),
                ]);

                $this->sendSse('complète', [
                    'conversation' => $this->assistantConsolePageService->mapConversationForRealtime($payload['conversation']),
                    'assistant_message' => $this->assistantConsolePageService->mapMessage($payload['assistant_message']),
                ]);
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'X-Accel-Buffering' => 'no',
                'Connection' => 'keep-alive',
            ]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function sendSse(string $event, array $data): void
    {
        echo 'event: '.$event."\n";
        echo 'data: '.json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n\n";

        if (function_exists('ob_flush')) {
            @ob_flush();
        }

        flush();
    }

    private function guardConversationOwnership(StoreAssistantMessageRequest $request): void
    {
        $conversationId = $request->integer('conversation_id') ?: null;

        if (! $conversationId) {
            return;
        }

        $exists = AssistantConversation::query()
            ->where('user_id', $request->user()->id)
            ->whereKey($conversationId)
            ->exists();

        if (! $exists) {
            throw (new ModelNotFoundException())->setModel(AssistantConversation::class, [$conversationId]);
        }
    }
}
