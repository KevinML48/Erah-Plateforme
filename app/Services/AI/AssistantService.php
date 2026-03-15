<?php

namespace App\Services\AI;

use App\Models\AssistantConversation;
use App\Models\AssistantMessage;
use App\Models\User;
use App\Services\AI\Providers\AssistantProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class AssistantService
{
    public function __construct(
        private readonly AssistantProvider $assistantProvider,
        private readonly AssistantContextService $assistantContextService,
        private readonly AssistantPromptBuilder $assistantPromptBuilder,
        private readonly AssistantFallbackService $assistantFallbackService,
        private readonly AssistantQueryClassifier $assistantQueryClassifier,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function send(User $user, string $message, ?int $conversationId = null): array
    {
        $session = $this->beginConversation($user, $message, $conversationId);

        $assistantResponse = $this->resolveAssistantResponse(
            user: $user,
            conversation: $session['conversation'],
            onDelta: null,
        );

        $assistantMessage = $this->storeAssistantMessage($session['conversation'], $assistantResponse);

        return [
            'conversation' => $session['conversation']->fresh(['latestMessage'])->loadCount('messages'),
            'user_message' => $session['user_message'],
            'assistant_message' => $assistantMessage,
        ];
    }

    /**
     * @param callable(string): void|null $onDelta
     * @return array<string, mixed>
     */
    public function stream(User $user, string $message, ?int $conversationId = null, ?callable $onDelta = null): array
    {
        $session = $this->beginConversation($user, $message, $conversationId);

        $assistantResponse = $this->resolveAssistantResponse(
            user: $user,
            conversation: $session['conversation'],
            onDelta: $onDelta,
        );

        $assistantMessage = $this->storeAssistantMessage($session['conversation'], $assistantResponse);

        return [
            'conversation' => $session['conversation']->fresh(['latestMessage'])->loadCount('messages'),
            'user_message' => $session['user_message'],
            'assistant_message' => $assistantMessage,
        ];
    }

    public function renameConversation(User $user, int $conversationId, string $title): AssistantConversation
    {
        $conversation = AssistantConversation::query()
            ->where('user_id', $user->id)
            ->findOrFail($conversationId);

        $conversation->forceFill([
            'title' => $this->sanitizeConversationTitle($title) ?: $conversation->title,
        ])->save();

        return $conversation->fresh(['latestMessage'])->loadCount('messages');
    }

    public function deleteConversation(User $user, int $conversationId): void
    {
        $conversation = AssistantConversation::query()
            ->where('user_id', $user->id)
            ->findOrFail($conversationId);

        $conversation->delete();
    }

    /**
     * @return array{conversation: AssistantConversation, user_message: AssistantMessage}
     */
    private function beginConversation(User $user, string $message, ?int $conversationId): array
    {
        if (! config('assistant.enabled', true)) {
            throw new RuntimeException("L assistant est temporairement désactivée.");
        }

        $conversation = $conversationId
            ? AssistantConversation::query()->where('user_id', $user->id)->findOrFail($conversationId)
            : AssistantConversation::query()->create([
                'user_id' => $user->id,
                'title' => $this->generateTitle($message),
                'provider' => $this->assistantProvider->name(),
                'model' => $this->assistantProvider->model(),
                'last_message_at' => now(),
            ]);

        $userMessage = $conversation->messages()->create([
            'role' => AssistantMessage::ROLE_USER,
            'content' => trim($message),
        ]);

        $conversation->forceFill([
            'last_message_at' => $userMessage->created_at,
        ])->save();

        return [
            'conversation' => $conversation->fresh(),
            'user_message' => $userMessage->fresh(),
        ];
    }

    private function resolveAssistantResponse(User $user, AssistantConversation $conversation, ?callable $onDelta): AssistantResponse
    {
        $context = $this->assistantContextService->build($user);
        $latestMessage = (string) optional($conversation->messages()->latest('id')->first())->content;
        $classification = $this->assistantQueryClassifier->classify($latestMessage);

        if ($classification->requiresGuardResponse()) {
            $guardedResponse = $this->assistantFallbackService->guardedReply($classification);

            if ($onDelta) {
                $this->simulateStream($guardedResponse->content, $onDelta);
            }

            return $this->sanitizeResponse($guardedResponse);
        }

        if (in_array('supporter', $classification->matchedTopics, true)) {
            $supporterResponse = $this->assistantFallbackService->reply(
                message: $latestMessage,
                user: $user,
                context: $context,
                classification: $classification,
            );
            $supporterResponse = $this->sanitizeResponse($supporterResponse);

            if ($onDelta) {
                $this->simulateStream($supporterResponse->content, $onDelta);
            }

            return $supporterResponse;
        }

        $messages = $this->messageStack($conversation, $context);

        if ($this->assistantProvider->configured()) {
            try {
                if ($onDelta && config('assistant.streaming.enabled', true)) {
                    return $this->sanitizeResponse($this->assistantProvider->stream($messages, $onDelta));
                }

                $response = $this->assistantProvider->generate($messages);
                $response = $this->sanitizeResponse($response);

                if ($onDelta) {
                    $this->simulateStream($response->content, $onDelta);
                }

                return $response;
            } catch (Throwable $exception) {
                Log::warning('assistant.provider_failed', [
                    'provider' => $this->assistantProvider->name(),
                    'message' => $exception->getMessage(),
                ]);

                if ($onDelta) {
                    throw new RuntimeException('Le flux IA a ete interrompu. Reessayez dans un instant.', previous: $exception);
                }

                if (! config('assistant.fallback.enabled', true)) {
                    throw new RuntimeException("Le provider IA est indisponible pour le moment.");
                }
            }
        }

        $fallbackResponse = $this->assistantFallbackService->reply(
            message: $latestMessage,
            user: $user,
            context: $context,
            classification: $classification,
        );
        $fallbackResponse = $this->sanitizeResponse($fallbackResponse);

        if ($onDelta) {
            $this->simulateStream($fallbackResponse->content, $onDelta);
        }

        return $fallbackResponse;
    }

    /**
     * @param array<string, mixed> $context
     * @return array<int, array{role: string, content: string}>
     */
    private function messageStack(AssistantConversation $conversation, array $context): array
    {
        $messages = [[
            'role' => AssistantMessage::ROLE_SYSTEM,
            'content' => $this->assistantPromptBuilder->build($context),
        ]];

        $window = config('assistant.memory.enabled', true)
            ? (int) config('assistant.memory.message_window', 12)
            : 1;

        $history = $conversation->messages()
            ->orderByDesc('id')
            ->limit(max(1, $window))
            ->get()
            ->reverse()
            ->values();

        foreach ($history as $message) {
            $messages[] = [
                'role' => $message->role,
                'content' => $message->content,
            ];
        }

        return $messages;
    }

    private function storeAssistantMessage(AssistantConversation $conversation, AssistantResponse $assistantResponse): AssistantMessage
    {
        return DB::transaction(function () use ($conversation, $assistantResponse): AssistantMessage {
            $assistantMessage = $conversation->messages()->create([
                'role' => AssistantMessage::ROLE_ASSISTANT,
                'content' => trim($assistantResponse->content),
                'provider' => $assistantResponse->provider,
                'model' => $assistantResponse->model,
                'prompt_tokens' => $assistantResponse->usage['prompt_tokens'] ?? null,
                'completion_tokens' => $assistantResponse->usage['completion_tokens'] ?? null,
                'metadata' => $assistantResponse->metadata,
            ]);

            $conversation->forceFill([
                'provider' => $assistantResponse->provider,
                'model' => $assistantResponse->model,
                'last_message_at' => $assistantMessage->created_at,
                'title' => $conversation->title ?: $this->generateTitle($assistantMessage->content),
            ])->save();

            return $assistantMessage->fresh();
        });
    }

    private function simulateStream(string $content, callable $onDelta): void
    {
        $chunks = preg_split('/(\s+)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
        $delay = max(0, (int) config('assistant.streaming.simulate_delay_ms', 16));

        foreach ($chunks as $chunk) {
            $onDelta($chunk);

            if ($delay > 0) {
                usleep($delay * 1000);
            }
        }
    }

    private function generateTitle(string $message): string
    {
        return $this->sanitizeConversationTitle($message) ?: 'Nouvelle conversation';
    }

    private function sanitizeConversationTitle(string $value): string
    {
        return Str::of(strip_tags($value))
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->limit(160, '')
            ->toString();
    }

    private function sanitizeResponse(AssistantResponse $response): AssistantResponse
    {
        $metadata = $response->metadata;
        $metadata['sources'] = collect($metadata['sources'] ?? [])
            ->map(function (mixed $source): mixed {
                if (! is_array($source)) {
                    return $source;
                }

                $source['url'] = $this->sanitizeLocalUrl($source['url'] ?? null);

                return $source;
            })
            ->values()
            ->all();
        $metadata['next_steps'] = collect($metadata['next_steps'] ?? [])
            ->map(fn (mixed $step): mixed => is_string($step) ? $this->sanitizeLocalUrlsInText($step) : $step)
            ->filter()
            ->values()
            ->all();

        return new AssistantResponse(
            content: $this->sanitizeLocalUrlsInText($response->content),
            provider: $response->provider,
            model: $response->model,
            metadata: $metadata,
            usage: $response->usage,
        );
    }

    private function sanitizeLocalUrlsInText(string $content): string
    {
        return preg_replace_callback(
            '#https?://[^\s\]\)]+#i',
            fn (array $matches): string => $this->sanitizeLocalUrl($matches[0]) ?? $matches[0],
            $content
        ) ?? $content;
    }

    private function sanitizeLocalUrl(?string $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        $url = trim((string) $url);

        if (Str::startsWith($url, '/')) {
            return $url;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $appHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        $localHosts = array_filter([$appHost, '127.0.0.1', 'localhost']);

        if (! in_array($host, $localHosts, true)) {
            return $url;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);
        $query = (string) parse_url($url, PHP_URL_QUERY);
        $fragment = (string) parse_url($url, PHP_URL_FRAGMENT);

        return ($path !== '' ? $path : '/')
            .($query !== '' ? '?'.$query : '')
            .($fragment !== '' ? '#'.$fragment : '');
    }
}
