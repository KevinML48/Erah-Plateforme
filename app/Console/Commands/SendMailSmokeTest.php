<?php

namespace App\Console\Commands;

use App\Mail\MailSmokeTestMailable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendMailSmokeTest extends Command
{
    protected $signature = 'app:mail-smoke-test
        {recipient : Adresse email de destination}
        {--queue : Enfile le mail au lieu de l\'envoyer immediatement}
        {--subject=Smoke test email ERAH : Sujet de l\'email de test}
        {--message=Verification manuelle du sous-systeme email ERAH avec la configuration mail active. : Corps du message de test}';

    protected $description = 'Envoie un email de verification simple en utilisant la configuration mail Laravel active.';

    public function handle(): int
    {
        $recipient = trim((string) $this->argument('recipient'));
        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error('Adresse email invalide.');

            return self::FAILURE;
        }

        $mailable = new MailSmokeTestMailable(
            subjectLine: (string) $this->option('subject'),
            messageLine: (string) $this->option('message'),
        );

        try {
            $mailer = Mail::to($recipient);

            if ((bool) $this->option('queue')) {
                $mailer->queue($mailable);

                Log::info('mail.smoke_test.queued', [
                    'recipient' => $recipient,
                    'mailer' => config('mail.default'),
                    'queue_connection' => config('queue.default'),
                ]);

                $this->info('Email de test place en queue.');
                $this->line('Destinataire: '.$recipient);
                $this->line('Mailer actif: '.(string) config('mail.default'));
                $this->line('Worker requis: php artisan queue:work --queue=default');

                return self::SUCCESS;
            }

            $mailer->send($mailable);

            Log::info('mail.smoke_test.sent', [
                'recipient' => $recipient,
                'mailer' => config('mail.default'),
                'queue_connection' => config('queue.default'),
            ]);

            $this->info('Email de test envoye.');
            $this->line('Destinataire: '.$recipient);
            $this->line('Mailer actif: '.(string) config('mail.default'));

            return self::SUCCESS;
        } catch (Throwable $exception) {
            Log::error('mail.smoke_test.failed', [
                'recipient' => $recipient,
                'mailer' => config('mail.default'),
                'queue_connection' => config('queue.default'),
                'error' => $exception->getMessage(),
            ]);

            report($exception);

            $this->error('Echec de l\'envoi de l\'email de test. Consultez les logs applicatifs.');

            return self::FAILURE;
        }
    }
}