<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendAdminOutboundEmailJob;
use App\Mail\AdminDirectMailMailable;
use App\Models\AdminOutboundEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class SendAdminOutboundEmailJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_marks_queued_email_as_sent_when_delivery_succeeds(): void
    {
        Mail::fake();

        $email = $this->createQueuedAdminEmail();

        $job = new SendAdminOutboundEmailJob($email->id);
        $job->queue = 'default';
        $job->handle();

        $email->refresh();

        self::assertSame(AdminOutboundEmail::STATUS_SENT, $email->status);
        self::assertNotNull($email->sent_at);
        self::assertNull($email->failed_at);
        self::assertNull($email->failure_reason);

        Mail::assertSent(AdminDirectMailMailable::class, 1);
    }

    public function test_job_marks_queued_email_as_failed_when_delivery_throws(): void
    {
        $email = $this->createQueuedAdminEmail();

        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new RuntimeException('resend api failure'));

        $job = new SendAdminOutboundEmailJob($email->id);
        $job->queue = 'default';

        try {
            $job->handle();
            self::fail('The job should rethrow the delivery exception.');
        } catch (RuntimeException $exception) {
            self::assertSame('resend api failure', $exception->getMessage());
        }

        $email->refresh();

        self::assertSame(AdminOutboundEmail::STATUS_FAILED, $email->status);
        self::assertNotNull($email->failed_at);
        self::assertSame('resend api failure', $email->failure_reason);
    }

    private function createQueuedAdminEmail(): AdminOutboundEmail
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $recipient = User::factory()->create([
            'name' => 'Recipient User',
            'email' => 'recipient@erah.test',
        ]);

        return AdminOutboundEmail::query()->create([
            'sender_admin_user_id' => $admin->id,
            'recipient_user_id' => $recipient->id,
            'recipient_email' => $recipient->email,
            'recipient_name' => $recipient->name,
            'subject' => 'Sujet en queue',
            'body_html' => '<p>Bonjour</p>',
            'body_text' => 'Bonjour',
            'category' => AdminOutboundEmail::CATEGORY_SUPPORT,
            'status' => AdminOutboundEmail::STATUS_QUEUED,
            'queued_at' => now(),
            'mailer' => 'resend',
            'provider' => 'resend',
            'meta' => [
                'confirm_token' => 'job-test-token',
            ],
        ]);
    }
}