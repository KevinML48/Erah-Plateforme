<?php

namespace Tests\Feature\Web;

use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClipCommentsInterfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_sees_collapsed_reply_controls_on_clip_page(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->create(['name' => 'Comment Author']);
        $clip = Clip::factory()->create(['created_by' => $author->id]);
        $comment = ClipComment::query()->create([
            'clip_id' => $clip->id,
            'user_id' => $author->id,
            'parent_id' => null,
            'body' => 'Commentaire principal.',
            'status' => ClipComment::STATUS_PUBLISHED,
        ]);

        $response = $this->actingAs($viewer)->get(route('clips.show', $clip->slug));

        $response->assertOk()
            ->assertSee('Commentaire principal.')
            ->assertSee('Repondre')
            ->assertSee('Annuler')
            ->assertSee('Repondre a Comment Author');
    }

    public function test_reply_validation_reopens_target_reply_panel(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->create(['name' => 'Comment Author']);
        $clip = Clip::factory()->create(['created_by' => $author->id]);
        $comment = ClipComment::query()->create([
            'clip_id' => $clip->id,
            'user_id' => $author->id,
            'parent_id' => null,
            'body' => 'Commentaire principal.',
            'status' => ClipComment::STATUS_PUBLISHED,
        ]);

        $response = $this->followingRedirects()
            ->actingAs($viewer)
            ->from(route('clips.show', $clip->slug))
            ->post(route('clips.comment', $clip->id), [
                'body' => '',
                'parent_id' => $comment->id,
                'comment_form_type' => 'reply',
            ]);

        $response->assertOk()
            ->assertSee('Masquer')
            ->assertSee('clip-reply-panel-'.$comment->id, false)
            ->assertSee('Votre reponse')
            ->assertSee('The body field is required.');
    }
}
