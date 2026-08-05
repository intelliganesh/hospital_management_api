<?php

namespace App\Providers;

use App\Events\MailEvent;
use App\Listeners\MailListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MailEvent::class => [
            MailListener::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
