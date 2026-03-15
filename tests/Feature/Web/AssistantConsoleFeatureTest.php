<?php

namespace Tests\Feature\Web;

use App\Models\AssistantConversation;
use App\Models\AssistantMessage;
use App\Models\User;
use Database\Seeders\HelpCenterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssistantConsoleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_console_assistant(): void
    {
        $this->get(route('assistant.index'))
            ->assertRedirectContains('/login');
    }

    public function test_authenticated_user_can_open_console_assistant_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('assistant.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Assistant/Show')
                ->where('page.hero.title', 'Un assistant conversationnel relie a votre espace.')
                ->where('page.availability.enabled', true));
    }

    public function test_user_can_send_message_and_persist_conversation_history(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('assistant.messages.store'), [
                'message' => 'Comment gagner des points avec les missions ?',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.conversation.message_count', 2)
            ->assertJsonPath('data.user_message.role', AssistantMessage::ROLE_USER)
            ->assertJsonPath('data.assistant_message.role', AssistantMessage::ROLE_ASSISTANT);

        $conversation = AssistantConversation::query()->where('user_id', $user->id)->firstOrFail();

        $this->assertSame('Comment gagner des points avec les missions ?', $conversation->title);
        $this->assertDatabaseCount('assistant_conversations', 1);
        $this->assertDatabaseCount('assistant_messages', 2);
        $this->assertDatabaseHas('assistant_messages', [
            'assistant_conversation_id' => $conversation->id,
            'role' => AssistantMessage::ROLE_USER,
            'content' => 'Comment gagner des points avec les missions ?',
        ]);
        $this->assertDatabaseHas('assistant_messages', [
            'assistant_conversation_id' => $conversation->id,
            'role' => AssistantMessage::ROLE_ASSISTANT,
            'provider' => 'knowledge-base',
        ]);
    }

    public function test_conversations_are_isolated_per_user(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $this->actingAs($owner)
            ->postJson(route('assistant.messages.store'), [
                'message' => 'Explique moi les paris.',
            ])
            ->assertOk();

        $conversation = AssistantConversation::query()->where('user_id', $owner->id)->firstOrFail();

        $this->actingAs($intruder)
            ->get(route('assistant.index', ['conversation' => $conversation->id]))
            ->assertNotFound();

        $this->actingAs($intruder)
            ->postJson(route('assistant.messages.store'), [
                'conversation_id' => $conversation->id,
                'message' => 'Je tente d ouvrir la conversation de quelqu un d autre.',
            ])
            ->assertNotFound();
    }

    public function test_user_can_rename_own_conversation(): void
    {
        $user = User::factory()->create();
        $conversation = AssistantConversation::query()->create([
            'user_id' => $user->id,
            'title' => 'Ancien titre',
            'last_message_at' => now(),
        ]);

        $this->actingAs($user)
            ->patchJson(route('assistant.conversations.update', $conversation), [
                'title' => 'Plan matchs et missions',
            ])
            ->assertOk()
            ->assertJsonPath('data.conversation.title', 'Plan matchs et missions');

        $this->assertDatabaseHas('assistant_conversations', [
            'id' => $conversation->id,
            'title' => 'Plan matchs et missions',
        ]);
    }

    public function test_user_can_delete_own_conversation_and_messages(): void
    {
        $user = User::factory()->create();
        $conversation = AssistantConversation::query()->create([
            'user_id' => $user->id,
            'title' => 'Conversation a supprimer',
            'last_message_at' => now(),
        ]);
        $message = AssistantMessage::query()->create([
            'assistant_conversation_id' => $conversation->id,
            'role' => AssistantMessage::ROLE_USER,
            'content' => 'Message temporaire',
        ]);

        $this->actingAs($user)
            ->delete(route('assistant.conversations.destroy', $conversation))
            ->assertNoContent();

        $this->assertDatabaseMissing('assistant_conversations', [
            'id' => $conversation->id,
        ]);
        $this->assertDatabaseMissing('assistant_messages', [
            'id' => $message->id,
        ]);
    }

    public function test_conversation_management_is_isolated_per_user(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $conversation = AssistantConversation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Conversation privee',
            'last_message_at' => now(),
        ]);

        $this->actingAs($intruder)
            ->patchJson(route('assistant.conversations.update', $conversation), [
                'title' => 'Tentative intrus',
            ])
            ->assertNotFound();

        $this->actingAs($intruder)
            ->delete(route('assistant.conversations.destroy', $conversation))
            ->assertNotFound();

        $this->assertDatabaseHas('assistant_conversations', [
            'id' => $conversation->id,
            'title' => 'Conversation privee',
        ]);
    }

    public function test_assistant_endpoint_returns_service_unavailable_when_feature_is_disabled(): void
    {
        config()->set('assistant.enabled', false);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('assistant.messages.store'), [
                'message' => 'Bonjour assistant',
            ])
            ->assertStatus(503)
            ->assertJsonPath('message', "L assistant est temporairement desactive.");
    }

    public function test_console_assistant_requests_precision_for_a_broad_question(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('assistant.messages.store'), [
                'message' => 'comment ca marche',
            ]);

        $response->assertOk();
        $this->assertStringContainsString(
            'tout est rassemble dans le meme espace pour suivre ta progression',
            (string) $response->json('data.assistant_message.content')
        );
    }

    public function test_console_assistant_guarantees_all_configured_starter_prompts(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $user = User::factory()->create();

        foreach ((array) config('assistant.ui.starter_prompts', []) as $prompt) {
            $response = $this->actingAs($user)
                ->postJson(route('assistant.messages.store'), [
                    'message' => $prompt,
                ]);

            $response->assertOk();

            $content = (string) $response->json('data.assistant_message.content');

            $this->assertNotSame('', trim($content), $prompt);
            $this->assertStringNotContainsString('ta question est assez large', $content, $prompt);
            $this->assertStringNotContainsString('Je n ai pas bien compris ta question', $content, $prompt);
            $this->assertStringNotContainsString('Je vois le sujet, mais je prefere rester prudent', $content, $prompt);
        }
    }

    public function test_console_assistant_does_not_answer_randomly_when_message_is_incomprehensible(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('assistant.messages.store'), [
                'message' => 'banane nuage moteur',
            ]);

        $response->assertOk();
        $this->assertStringContainsString(
            'Je n ai pas bien compris ta question',
            (string) $response->json('data.assistant_message.content')
        );
    }

    public function test_console_assistant_can_answer_with_contextualized_profile_guidance(): void
    {
        $user = User::factory()->create([
            'bio' => null,
            'avatar_path' => null,
            'twitter_url' => null,
            'instagram_url' => null,
            'tiktok_url' => null,
            'discord_url' => null,
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('assistant.messages.store'), [
                'message' => 'Comment ameliorer mon profil ?',
            ]);

        $response->assertOk();

        $content = (string) $response->json('data.assistant_message.content');

        $this->assertStringContainsString('Ajoutez une bio courte', $content);
        $this->assertStringNotContainsString('127.0.0.1', $content);
    }

    public function test_console_assistant_can_explain_how_to_become_supporter(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('assistant.messages.store'), [
                'message' => 'Comment devenir supporter ?',
            ]);

        $response->assertOk();
        $this->assertStringContainsString(
            'page Supporter',
            (string) $response->json('data.assistant_message.content')
        );
    }
}
