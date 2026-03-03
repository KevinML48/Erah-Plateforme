<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MarketingContactMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param array{name:string,email:string,subject:string,message:string} $payload
     */
    public function __construct(
        public array $payload
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[ERAH Contact] '.$this->payload['subject'],
            replyTo: [$this->payload['email']],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.marketing.contact',
            with: [
                'payload' => $this->payload,
            ],
        );
    }
}

