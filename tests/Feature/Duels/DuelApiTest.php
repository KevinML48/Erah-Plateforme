<?php

namespace Tests\Feature\Duels;

use App\Jobs\ExpireDuelJob;
use App\Models\Duel;
use App\Models\DuelEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DuelApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_challenger_can_create_duel_and_creation_is_idempotent_with_notification(): void
    {
        Queue::fake();

        $challenger = User::factory()->create(['name' => 'Challenger']);
        $challenged = User::factory()->create(['name' => 'Challenged']);

        Sanctum::actingAs($challenger);

        $payload = [
            'challenged_user_id' => $challenged->id,
            'idempotency_key' => 'duel-create-001',
            'message' => 'Ready for a duel?',
            'expires_in_minutes' => 90,
        ];

        $first = $this->postJson('/api/duels', $payload);
        $first->assertCreated()
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('data.status', Duel::STATUS_PENDING)
            ->assertJsonPath('data.challenger.id', $challenger->id)
            ->assertJsonPath('data.challenged.id', $challenged->id);

        $second = $this->postJson('/api/duels', $payload);
        $second->assertOk()
            ->assertJsonPath('idempotent', true);

        $duelId = (int) $first->json('data.id');

        $this->assertDatabaseCount('duels', 1);
        $this->assertDatabaseHas('duel_events', [
            'duel_id' => $duelId,
            'event_type' => 'created',
            'actor_id' => $challenger->id,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $challenged->id,
            'category' => 'duel',
        ]);

        Queue::assertPushed(ExpireDuelJob::class, function (ExpireDuelJob $job) use ($duelId) {
            return $job->duelId === $duelId;
        });
    }

    public function test_accept_and_refuse_permissions_and_idempotence_are_enforced(): void
    {
        $challenger = User::factory()->create();
        $challenged = User::factory()->create();
        $outsider = User::factory()->create();

        $duel = Duel::factory()->create([
            'challenger_id' => $challenger->id,
            'challenged_id' => $challenged->id,
            'status' => Duel::STATUS_PENDING,
            'requested_at' => now()->subMinutes(2),
            'expires_at' => now()->addMinutes(10),
        ]);

        Sanctum::actingAs($challenger);
        $this->postJson('/api/duels/'.$duel->id.'/accept')->assertForbidden();

        Sanctum::actingAs($outsider);
        $this->postJson('/api/duels/'.$duel->id.'/refuse')->assertForbidden();

        Sanctum::actingAs($challenged);
        $firstAccept = $this->postJson('/api/duels/'.$duel->id.'/accept');
        $firstAccept->assertOk()
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('data.status', Duel::STATUS_ACCEPTED);

        $secondAccept = $this->postJson('/api/duels/'.$duel->id.'/accept');
        $secondAccept->assertOk()
            ->assertJsonPath('idempotent', true)
            ->assertJsonPath('data.status', Duel::STATUS_ACCEPTED);

        $this->postJson('/api/duels/'.$duel->id.'/refuse')
            ->assertStatus(422);

        $acceptedEvents = DuelEvent::query()
            ->where('duel_id', $duel->id)
            ->where('event_type', 'accepted')
            ->count();

        $this->assertSame(1, $acceptedEvents);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'duels.accepted',
        ]);
    }

    public function test_expire_job_updates_status_and_list_endpoint_filters_by_status(): void
    {
        $challenger = User::factory()->create();
        $challenged = User::factory()->create();

        $expiredCandidate = Duel::factory()->create([
            'challenger_id' => $challenger->id,
            'challenged_id' => $challenged->id,
            'status' => Duel::STATUS_PENDING,
            'requested_at' => now()->subHours(2),
            'expires_at' => now()->subMinute(),
        ]);

        Duel::factory()->create([
            'challenger_id' => $challenger->id,
            'challenged_id' => User::factory()->create()->id,
            'status' => Duel::STATUS_PENDING,
            'requested_at' => now()->subMinutes(5),
            'expires_at' => now()->addMinutes(30),
        ]);

        $job = new ExpireDuelJob($expiredCandidate->id);
        $job->handle(app(\App\Application\Actions\Duels\ExpireDuelAction::class));
        $job->handle(app(\App\Application\Actions\Duels\ExpireDuelAction::class));

        $expiredCandidate->refresh();
        $this->assertSame(Duel::STATUS_EXPIRED, $expiredCandidate->status);
        $this->assertNotNull($expiredCandidate->expired_at);

        $expiredEvents = DuelEvent::query()
            ->where('duel_id', $expiredCandidate->id)
            ->where('event_type', 'expired')
            ->count();

        $this->assertSame(1, $expiredEvents);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $challenger->id,
            'category' => 'duel',
        ]);

        Sanctum::actingAs($challenged);
        $this->postJson('/api/duels/'.$expiredCandidate->id.'/accept')
            ->assertStatus(422);

        Sanctum::actingAs($challenger);
        $response = $this->getJson('/api/duels?status=expired');
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $expiredCandidate->id)
            ->assertJsonPath('data.0.status', Duel::STATUS_EXPIRED);
    }
}
