<?php

namespace Tests\Feature\Clips;

use App\Models\Clip;
use App\Models\CommunityRewardGrant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClipApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_only_can_manage_clips_and_slug_generation_is_unique(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $regularUser = User::factory()->create(['role' => User::ROLE_USER]);

        Sanctum::actingAs($regularUser);
        $forbidden = $this->postJson('/api/admin/clips', [
            'title' => 'Ace Move',
            'video_url' => 'https://videos.example.test/ace-1.mp4',
        ]);
        $forbidden->assertForbidden();

        Sanctum::actingAs($admin);

        $firstCreate = $this->postJson('/api/admin/clips', [
            'title' => 'Ace Move',
            'video_url' => 'https://videos.example.test/ace-1.mp4',
            'description' => 'First clip',
        ]);

        $firstCreate->assertCreated()
            ->assertJsonPath('data.slug', 'ace-move')
            ->assertJsonPath('data.is_published', false);

        $secondCreate = $this->postJson('/api/admin/clips', [
            'title' => 'Ace Move',
            'video_url' => 'https://videos.example.test/ace-2.mp4',
            'description' => 'Second clip',
        ]);

        $secondCreate->assertCreated();
        $this->assertNotSame(
            $firstCreate->json('data.slug'),
            $secondCreate->json('data.slug')
        );

        $clipId = (int) $firstCreate->json('data.id');

        $publish = $this->postJson('/api/admin/clips/'.$clipId.'/publish');
        $publish->assertOk()
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('data.is_published', true);

        $publishAgain = $this->postJson('/api/admin/clips/'.$clipId.'/publish');
        $publishAgain->assertOk()->assertJsonPath('idempotent', true);

        Sanctum::actingAs($regularUser);
        $this->putJson('/api/admin/clips/'.$clipId, [
            'title' => 'Updated title',
        ])->assertForbidden();
        $this->deleteJson('/api/admin/clips/'.$clipId)->assertForbidden();

        $this->assertDatabaseCount('clips', 2);
        $this->assertDatabaseHas('audit_logs', ['action' => 'clips.created']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'clips.published']);
    }

    public function test_like_and_favorite_are_idempotent_and_counters_stay_consistent(): void
    {
        $user = User::factory()->create();
        $clip = Clip::factory()->create([
            'likes_count' => 0,
            'favorites_count' => 0,
            'comments_count' => 0,
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/clips/'.$clip->id.'/like')
            ->assertOk()
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('counts.likes_count', 1);

        $this->postJson('/api/clips/'.$clip->id.'/like')
            ->assertOk()
            ->assertJsonPath('idempotent', true)
            ->assertJsonPath('counts.likes_count', 1);

        $this->postJson('/api/clips/'.$clip->id.'/favorite')
            ->assertOk()
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('counts.favorites_count', 1);

        $this->postJson('/api/clips/'.$clip->id.'/favorite')
            ->assertOk()
            ->assertJsonPath('idempotent', true)
            ->assertJsonPath('counts.favorites_count', 1);

        $this->assertDatabaseCount('clip_likes', 1);
        $this->assertDatabaseCount('clip_favorites', 1);

        $this->deleteJson('/api/clips/'.$clip->id.'/like')
            ->assertOk()
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('counts.likes_count', 0);

        $this->deleteJson('/api/clips/'.$clip->id.'/like')
            ->assertOk()
            ->assertJsonPath('idempotent', true)
            ->assertJsonPath('counts.likes_count', 0);

        $this->deleteJson('/api/clips/'.$clip->id.'/favorite')
            ->assertOk()
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('counts.favorites_count', 0);

        $this->deleteJson('/api/clips/'.$clip->id.'/favorite')
            ->assertOk()
            ->assertJsonPath('idempotent', true)
            ->assertJsonPath('counts.favorites_count', 0);

        $clip->refresh();
        $this->assertSame(0, $clip->likes_count);
        $this->assertSame(0, $clip->favorites_count);
    }

    public function test_like_reward_is_granted_only_once_even_after_unlike_and_relike(): void
    {
        $user = User::factory()->create();
        $clip = Clip::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson('/api/clips/'.$clip->id.'/like')->assertOk();
        $this->deleteJson('/api/clips/'.$clip->id.'/like')->assertOk();
        $this->postJson('/api/clips/'.$clip->id.'/like')->assertOk();

        $this->assertSame(
            1,
            CommunityRewardGrant::query()
                ->where('user_id', $user->id)
                ->where('domain', 'clips')
                ->where('action', 'like')
                ->count()
        );

        $this->assertDatabaseHas('user_reward_wallets', [
            'user_id' => $user->id,
            'balance' => 10,
        ]);
    }

    public function test_comment_delete_requires_owner_or_admin_and_updates_counter(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $clip = Clip::factory()->create(['comments_count' => 0]);

        Sanctum::actingAs($owner);
        $commentResponse = $this->postJson('/api/clips/'.$clip->id.'/comments', [
            'body' => 'Great play',
        ]);
        $commentResponse->assertCreated();
        $commentId = (int) $commentResponse->json('data.id');

        $clip->refresh();
        $this->assertSame(1, $clip->comments_count);

        Sanctum::actingAs($otherUser);
        $this->deleteJson('/api/clips/'.$clip->id.'/comments/'.$commentId)
            ->assertForbidden();

        Sanctum::actingAs($admin);
        $this->deleteJson('/api/clips/'.$clip->id.'/comments/'.$commentId)
            ->assertOk()
            ->assertJsonPath('counts.comments_count', 0);

        $this->assertDatabaseMissing('clip_comments', ['id' => $commentId]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'clips.comment.deleted']);
    }

    public function test_comment_rewards_are_shared_between_main_comment_and_reply_and_reply_depth_is_limited(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $clip = Clip::factory()->create(['comments_count' => 0]);

        Sanctum::actingAs($firstUser);
        $firstComment = $this->postJson('/api/clips/'.$clip->id.'/comments', [
            'body' => 'Premier commentaire',
        ]);
        $firstComment->assertCreated();

        Sanctum::actingAs($secondUser);
        $secondComment = $this->postJson('/api/clips/'.$clip->id.'/comments', [
            'body' => 'Deuxieme commentaire',
        ]);
        $secondComment->assertCreated();

        Sanctum::actingAs($firstUser);
        $reply = $this->postJson('/api/clips/'.$clip->id.'/comments', [
            'body' => 'Reponse rapide',
            'parent_id' => $secondComment->json('data.id'),
        ]);
        $reply->assertCreated();

        Sanctum::actingAs($secondUser);
        $tooDeep = $this->postJson('/api/clips/'.$clip->id.'/comments', [
            'body' => 'Je reponds a la reponse',
            'parent_id' => $reply->json('data.id'),
        ]);

        $tooDeep->assertStatus(422)
            ->assertJsonPath('message', 'Maximum reply depth reached.');

        $this->assertSame(
            1,
            CommunityRewardGrant::query()
                ->where('user_id', $firstUser->id)
                ->where('domain', 'clips')
                ->where('action', 'comment')
                ->count()
        );

        $this->assertDatabaseHas('user_reward_wallets', [
            'user_id' => $firstUser->id,
            'balance' => 20,
        ]);
    }

    public function test_public_feed_and_detail_expose_only_published_clips(): void
    {
        $popular = Clip::factory()->create([
            'title' => 'Popular Clip',
            'likes_count' => 12,
            'favorites_count' => 7,
            'comments_count' => 4,
        ]);
        $recent = Clip::factory()->create([
            'title' => 'Recent Clip',
            'likes_count' => 2,
            'favorites_count' => 1,
            'comments_count' => 0,
        ]);
        $hidden = Clip::factory()->unpublished()->create([
            'title' => 'Hidden Clip',
        ]);

        $popularFeed = $this->getJson('/api/clips?sort=popular');
        $popularFeed->assertOk()
            ->assertJsonPath('data.0.id', $popular->id)
            ->assertJsonCount(2, 'data');

        $detail = $this->getJson('/api/clips/'.$recent->slug);
        $detail->assertOk()
            ->assertJsonPath('data.id', $recent->id)
            ->assertJsonPath('data.slug', $recent->slug);

        $this->getJson('/api/clips/'.$hidden->slug)->assertNotFound();
    }
}
