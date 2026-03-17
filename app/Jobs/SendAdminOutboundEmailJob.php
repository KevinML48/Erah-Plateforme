<?php

namespace App\Jobs;

use App\Mail\AdminDirectMailMailable;
use App\Models\AdminOutboundEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendAdminOutboundEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [30, 120, 300];

    public function __construct(
        public readonly int $adminOutboundEmailId
    ) {
    }

    public function handle(): void
    {
        $email = DB::transaction(function (): ?AdminOutboundEmail {
            $record = AdminOutboundEmail::query()
                ->whereKey($this->adminOutboundEmailId)
                ->lockForUpdate()
                ->first();

            if (! $record) {
                return null;
            }

            if ($record->status === AdminOutboundEmail::STATUS_SENT) {
                return null;
            }

            if (! in_array((string) $record->status, [
                AdminOutboundEmail::STATUS_DRAFT,
                AdminOutboundEmail::STATUS_QUEUED,
                AdminOutboundEmail::STATUS_FAILED,
            ], true)) {
                return null;
            }

            return $record;
        });

        if (! $email) {
            return;
        }

        try {
            $pendingMail = Mail::to($email->recipient_email, $email->recipient_name);

            if ((bool) data_get($email->meta, 'cc_admin') && filled(config('mail.contact.address'))) {
                $pendingMail->cc(
                    (string) config('mail.contact.address'),
                    config('mail.contact.name')
                );
            }

            $sentMessage = $pendingMail->send(new AdminDirectMailMailable($email));

            $email->forceFill([
                'status' => AdminOutboundEmail::STATUS_SENT,
                'sent_at' => now(),
                'failed_at' => null,
                'failure_reason' => null,
                'provider_message_id' => is_object($sentMessage) && method_exists($sentMessage, 'getMessageId') ? $sentMessage->getMessageId() : null,
            ])->save();

            Log::info('email_admin_sent', [
                'admin_id' => $email->sender_admin_user_id,
                'recipient_email' => $email->recipient_email,
                'record_id' => $email->id,
                'status' => $email->status,
            ]);
        } catch (Throwable $exception) {
            $email->forceFill([
                'status' => AdminOutboundEmail::STATUS_FAILED,
                'failed_at' => now(),
                'failure_reason' => $exception->getMessage(),
            ])->save();

            Log::error('email_admin_failed', [
                'admin_id' => $email->sender_admin_user_id,
                'recipient_email' => $email->recipient_email,
                'record_id' => $email->id,
                'status' => $email->status,
            ]);

            throw $exception;
        }
    }
}