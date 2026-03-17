<?php

namespace App\Mail;

use App\Models\AdminOutboundEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminDirectMailMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly AdminOutboundEmail $email
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->email->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.direct-mail',
            with: [
                'emailRecord' => $this->email,
            ],
        );
    }
}