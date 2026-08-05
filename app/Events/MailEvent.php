<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class MailEvent
{
    use Dispatchable, SerializesModels;

    public $recipient;    // Email address of the recipient
    public $mailable;     // Mailable class (any email message)

    /**
     * Create a new event instance.
     *
     * @param string $recipient
     * @param mixed $mailable
     */
    public function __construct(string $recipient, $mailable)
    {
        $this->recipient = $recipient;
        $this->mailable = $mailable;
    }
}
