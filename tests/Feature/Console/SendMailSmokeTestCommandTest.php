<?php

namespace Tests\Feature\Console;

use App\Mail\MailSmokeTestMailable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendMailSmokeTestCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_smoke_test_command_sends_an_email_immediately(): void
    {
        Mail::fake();

        config([
            'mail.default' => 'log',
            'queue.default' => 'sync',
        ]);

        $this->artisan('app:mail-smoke-test', [
            'recipient' => 'destinataire@example.com',
        ])->assertExitCode(0);

        Mail::assertSent(MailSmokeTestMailable::class, 1);
    }

    public function test_smoke_test_command_queues_an_email_when_requested(): void
    {
        Mail::fake();

        config([
            'mail.default' => 'log',
            'queue.default' => 'database',
        ]);

        $this->artisan('app:mail-smoke-test', [
            'recipient' => 'destinataire@example.com',
            '--queue' => true,
        ])->assertExitCode(0);

        Mail::assertQueued(MailSmokeTestMailable::class, 1);
    }

    public function test_smoke_test_command_rejects_invalid_email_address(): void
    {
        Mail::fake();

        $this->artisan('app:mail-smoke-test', [
            'recipient' => 'adresse-invalide',
        ])->assertExitCode(1);

        Mail::assertNothingSent();
    }
}