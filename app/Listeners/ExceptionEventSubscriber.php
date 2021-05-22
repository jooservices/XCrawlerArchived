<?php

namespace App\Listeners;

use App\Events\ExceptionEvent;
use App\Notifications\ExceptionNotifcation;
use Illuminate\Support\Facades\Notification;

class ExceptionEventSubscriber
{
    public function hasException(ExceptionEvent $event)
    {
        Notification::route('slack', config('services.slack.exceptions'))
            ->notify(new ExceptionNotifcation($event->exception));
    }

    public function subscribe($events)
    {
        $events->listen([ExceptionEvent::class], self::class . '@hasException');
    }
}
