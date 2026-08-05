<?php

namespace App\Listeners;

use App\Events\MailEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param MailEvent $event
     */
    public function handle(MailEvent $event)
    {
        // Send the mailable to the recipient
        Mail::to($event->recipient)->send($event->mailable);
    }
}
