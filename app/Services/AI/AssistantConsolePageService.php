<?php

namespace App\Services\AI;

use App\Models\AssistantConversation;
use App\Models\AssistantMessage;
use App\Models\HelpArticle;
use App\Models\User;

class AssistantConsolePageService
{
    public function __construct(
        private readonly AssistantContextService $assistantContextService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(User $user, ?int $conversationId = null, ?string $articleSlug = null, ?string $prompt = null): array
    {
        $selectedConversation = null;

        if ($conversationId) {
            $selectedConversation = AssistantConversation::query()
                ->where('user_id', $user->id)
                ->with(['messages' => fn ($query) => $query->orderBy('id')])
                ->findOrFail($conversationId);
        }

        $conversations = AssistantConversation::query()
            ->where('user_id', $user->id)
            ->with('latestMessage')
            ->withCount('messages')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->limit((int) config('assistant.ui.conversation_limit', 24))
            ->get();

        $focusedArticle = null;

        if (filled($articleSlug)) {
            $focusedArticle = HelpArticle::query()
                ->published()
                ->where('slug', $articleSlug)
                ->first();
        }

        return [
            'availability' => [
                'enabled' => (bool) config('assistant.enabled', true),
                'provider' => (string) config('assistant.provider', 'none'),
                'model' => (string) config('assistant.model', 'gpt-4.1-mini'),
                'streaming' => (bool) config('assistant.streaming.enabled', true),
                'personalization' => (bool) config('assistant.personalization.enabled', true),
                'memory' => (bool) config('assistant.memory.enabled', true),
            ],
            'hero' => [
                'eyebrow' => 'ERAH Assistant',
                'title' => 'Un assistant conversationnel relie a votre espace.',
                'description' => "Posez une question comme a une vraie personne. L assistant s appuie sur la logique ERAH, la base de connaissance et votre contexte disponible sans jamais inventer.",
            ],
            'starter_prompts' => config('assistant.ui.starter_prompts', []),
            'prefill_prompt' => $prompt,
            'focused_article' => $focusedArticle ? [
                'title' => $focusedArticle->title,
                'summary' => $focusedArticle->short_answer ?: $focusedArticle->summary,
                'url' => route('help.index', ['article' => $focusedArticle->slug]).'#faq-center',
            ] : null,
            'sidebar' => $this->assistantContextService->pageSidebar($user),
            'conversations' => $conversations
                ->map(fn (AssistantConversation $conversation) => $this->mapConversationSummary($conversation, $selectedConversation?->id))
                ->values()
                ->all(),
            'selected_conversation' => $selectedConversation ? $this->mapSelectedConversation($selectedConversation) : null,
            'endpoints' => [
                'index' => route('assistant.index'),
                'store_message' => route('assistant.messages.store'),
                'stream_message' => route('assistant.messages.stream'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapConversationSummary(AssistantConversation $conversation, ?int $selectedConversationId): array
    {
        $latestMessage = $conversation->latestMessage;

        return [
            'id' => $conversation->id,
            'title' => $conversation->title,
            'is_active' => $selectedConversationId === $conversation->id,
            'message_count' => (int) ($conversation->messages_count ?? 0),
            'last_message_preview' => $latestMessage ? str($latestMessage->content)->squish()->limit(96)->toString() : null,
            'last_message_role' => $latestMessage?->role,
            'last_message_at' => optional($conversation->last_message_at)->toIso8601String(),
            'url' => route('assistant.index', ['conversation' => $conversation->id]),
            'rename_url' => route('assistant.conversations.update', $conversation),
            'delete_url' => route('assistant.conversations.destroy', $conversation),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapSelectedConversation(AssistantConversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'title' => $conversation->title,
            'provider' => $conversation->provider,
            'model' => $conversation->model,
            'rename_url' => route('assistant.conversations.update', $conversation),
            'delete_url' => route('assistant.conversations.destroy', $conversation),
            'messages' => $conversation->messages
                ->map(fn (AssistantMessage $message) => $this->mapMessage($message))
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapConversationForRealtime(AssistantConversation $conversation): array
    {
        $conversation->loadMissing('latestMessage');

        return $this->mapConversationSummary($conversation, $conversation->id);
    }

    /**
     * @return array<string, mixed>
     */
    public function mapMessage(AssistantMessage $message): array
    {
        return [
            'id' => $message->id,
            'role' => $message->role,
            'content' => $message->content,
            'provider' => $message->provider,
            'model' => $message->model,
            'metadata' => $message->metadata ?? [],
            'created_at' => optional($message->created_at)->toIso8601String(),
        ];
    }
}
