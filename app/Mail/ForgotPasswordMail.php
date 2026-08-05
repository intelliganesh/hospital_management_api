<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
// use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
// use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
// use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPasswordMail extends Mailable
{
    //implements ShouldQueue
    // use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    /**
     * Summary of __construct
     * @param \App\Models\User $user
     */
    public function __construct(User $user, $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Summary of envelope
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Forgot Password',
        );
    }

    /**
     * Summary of content
     * @return Content
     */
    public function content(): Content
    {

        return new Content(
            view: 'templates.forgot_password',
            with: [
                "name" => $this->user->name,
                "email" => $this->user->email,
                "verificationUrl" => $this->verificationUrl,
                "expiration" => now()->addMinutes(60)->format('Y-m-d H:i:s')
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
