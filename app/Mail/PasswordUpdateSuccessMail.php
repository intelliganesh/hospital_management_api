<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
// use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
// use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
// use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordUpdateSuccessMail extends Mailable
{
    //implements ShouldQueue
    // use Queueable, SerializesModels;

    public $user;

    /**
     * Summary of __construct
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Summary of envelope
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Update Successfully',
        );
    }

    /**
     * Summary of content
     * @return Content
     */
    public function content(): Content
    {

        return new Content(
            view: 'templates.password_update_success',
            with: [
                "name" => $this->user->name,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
