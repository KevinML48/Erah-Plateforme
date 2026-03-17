<?php

namespace Tests\Feature\Web;

use App\Jobs\SendAdminOutboundEmailJob;
use App\Mail\AdminDirectMailMailable;
use App\Models\AdminOutboundEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use Tests\TestCase;

class AdminEmailFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_email_pages(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)
            ->get(route('admin.emails.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.emails.create'))
            ->assertForbidden();
    }

    public function test_admin_can_preview_internal_user_email_and_create_a_draft(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $recipient = User::factory()->create([
            'name' => 'Recipient User',
            'email' => 'recipient@erah.test',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.emails.preview'), [
            'submission_token' => $this->composeToken($admin),
            'recipient_user_id' => $recipient->id,
            'subject' => 'Support {name}',
            'body_html' => 'Bonjour {name},<br>message de test.',
            'category' => AdminOutboundEmail::CATEGORY_SUPPORT,
            'template_key' => 'support-response',
            'cc_admin' => '1',
        ]);

        $response->assertOk()->assertSee('Apercu avant envoi');

        $this->assertDatabaseHas('admin_outbound_emails', [
            'sender_admin_user_id' => $admin->id,
            'recipient_user_id' => $recipient->id,
            'recipient_email' => 'recipient@erah.test',
            'status' => AdminOutboundEmail::STATUS_DRAFT,
            'category' => AdminOutboundEmail::CATEGORY_SUPPORT,
        ]);
    }

    public function test_admin_can_send_sync_email_and_mark_it_sent(): void
    {
        Mail::fake();

        config([
            'queue.default' => 'sync',
            'mail.default' => 'log',
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $draft = $this->createDraftForAdmin($admin);

        $this->actingAs($admin)
            ->post(route('admin.emails.send', $draft), [
                'confirm_token' => data_get($draft->meta, 'confirm_token'),
            ])
            ->assertRedirect(route('admin.emails.show', $draft));

        $draft->refresh();

        self::assertSame(AdminOutboundEmail::STATUS_SENT, $draft->status);
        self::assertNotNull($draft->sent_at);

        Mail::assertSent(AdminDirectMailMailable::class, 1);
    }

    public function test_admin_can_queue_email_when_queue_connection_is_active(): void
    {
        Queue::fake();

        config([
            'queue.default' => 'database',
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $draft = $this->createDraftForAdmin($admin);

        $this->actingAs($admin)
            ->post(route('admin.emails.send', $draft), [
                'confirm_token' => data_get($draft->meta, 'confirm_token'),
            ])
            ->assertRedirect(route('admin.emails.show', $draft));

        $draft->refresh();

        self::assertSame(AdminOutboundEmail::STATUS_QUEUED, $draft->status);
        self::assertNotNull($draft->queued_at);

        Queue::assertPushed(SendAdminOutboundEmailJob::class, function (SendAdminOutboundEmailJob $job) use ($draft): bool {
            return $job->adminOutboundEmailId === $draft->id;
        });
    }

    public function test_admin_can_send_to_external_email(): void
    {
        Mail::fake();

        config([
            'queue.default' => 'sync',
            'mail.default' => 'log',
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->post(route('admin.emails.preview'), [
            'submission_token' => $this->composeToken($admin),
            'recipient_email' => 'external@example.com',
            'recipient_name' => 'External Person',
            'subject' => 'Compte {platform_name}',
            'body_html' => 'Bonjour {name}',
            'category' => AdminOutboundEmail::CATEGORY_ACCOUNT,
        ])->assertOk();

        $draft = AdminOutboundEmail::query()->latest('id')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.emails.send', $draft), [
                'confirm_token' => data_get($draft->meta, 'confirm_token'),
            ])
            ->assertRedirect(route('admin.emails.show', $draft));

        $draft->refresh();

        self::assertSame(AdminOutboundEmail::STATUS_SENT, $draft->status);
        self::assertNull($draft->recipient_user_id);
        self::assertSame('external@example.com', $draft->recipient_email);
    }

    public function test_failed_send_keeps_record_and_marks_it_failed(): void
    {
        config([
            'queue.default' => 'sync',
            'mail.default' => 'log',
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $draft = $this->createDraftForAdmin($admin);

        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new RuntimeException('resend failure'));

        $this->actingAs($admin)
            ->post(route('admin.emails.send', $draft), [
                'confirm_token' => data_get($draft->meta, 'confirm_token'),
            ])
            ->assertRedirect(route('admin.emails.show', $draft))
            ->assertSessionHas('error');

        $draft->refresh();

        self::assertSame(AdminOutboundEmail::STATUS_FAILED, $draft->status);
        self::assertNotNull($draft->failed_at);
        self::assertSame('resend failure', $draft->failure_reason);
    }

    public function test_admin_can_retry_failed_email(): void
    {
        Mail::fake();

        config([
            'queue.default' => 'sync',
            'mail.default' => 'log',
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $draft = $this->createDraftForAdmin($admin);

        $draft->forceFill([
            'status' => AdminOutboundEmail::STATUS_FAILED,
            'failed_at' => now(),
            'failure_reason' => 'previous failure',
        ])->save();

        $this->actingAs($admin)
            ->post(route('admin.emails.retry', $draft))
            ->assertRedirect(route('admin.emails.show', $draft));

        $draft->refresh();

        self::assertSame(AdminOutboundEmail::STATUS_SENT, $draft->status);
        self::assertNull($draft->failure_reason);

        Mail::assertSent(AdminDirectMailMailable::class, 1);
    }

    public function test_duplicate_compose_submission_token_does_not_create_second_draft(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $recipient = User::factory()->create();
        $token = $this->composeToken($admin);

        $payload = [
            'submission_token' => $token,
            'recipient_user_id' => $recipient->id,
            'subject' => 'Sujet',
            'body_html' => 'Contenu',
            'category' => AdminOutboundEmail::CATEGORY_OTHER,
        ];

        $this->actingAs($admin)
            ->post(route('admin.emails.preview'), $payload)
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.emails.preview'), $payload)
            ->assertRedirect(route('admin.emails.create'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('admin_outbound_emails', 1);
    }

    public function test_admin_can_view_history_and_email_detail(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $email = AdminOutboundEmail::query()->create([
            'sender_admin_user_id' => $admin->id,
            'recipient_email' => 'audit@example.com',
            'recipient_name' => 'Audit User',
            'subject' => 'Historique',
            'body_html' => '<p>Bonjour</p>',
            'body_text' => 'Bonjour',
            'category' => AdminOutboundEmail::CATEGORY_SUPPORT,
            'status' => AdminOutboundEmail::STATUS_SENT,
            'mailer' => 'resend',
            'provider' => 'resend',
            'sent_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.emails.index'))
            ->assertOk()
            ->assertSee('Historique')
            ->assertSee('audit@example.com');

        $this->actingAs($admin)
            ->get(route('admin.emails.show', $email))
            ->assertOk()
            ->assertSee('Historique')
            ->assertSee('audit@example.com');
    }

    private function composeToken(User $admin): string
    {
        $this->actingAs($admin)
            ->get(route('admin.emails.create'))
            ->assertOk();

        return (string) collect(session('admin_email_submission_tokens', []))->last();
    }

    private function createDraftForAdmin(User $admin): AdminOutboundEmail
    {
        $recipient = User::factory()->create([
            'name' => 'Recipient User',
            'email' => 'recipient@erah.test',
        ]);

        $this->actingAs($admin)->post(route('admin.emails.preview'), [
            'submission_token' => $this->composeToken($admin),
            'recipient_user_id' => $recipient->id,
            'subject' => 'Sujet de test',
            'body_html' => 'Bonjour {name}',
            'category' => AdminOutboundEmail::CATEGORY_SUPPORT,
        ])->assertOk();

        return AdminOutboundEmail::query()->latest('id')->firstOrFail();
    }
}