<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GeneralEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private readonly string $emailSubject,
        private readonly string $emailBody
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'templates.general_email',
            with: [
                'subject' => $this->emailSubject,
                'body' => $this->emailBody,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
