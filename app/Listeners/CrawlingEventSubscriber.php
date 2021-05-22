<?php

namespace App\Listeners;

use App\Events\Jav\OnejavDailyCompletedEvent;
use App\Events\OnejavNewCompletedEvent;
use App\Models\Onejav;
use App\Notifications\CrawlingCompletedNotification;
use App\Services\Jav\OnejavService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class CrawlingEventSubscriber
{
    public function onejav($event)
    {
        if ($event instanceof OnejavDailyCompletedEvent) {
            $data['title'] = 'Daily ' . Carbon::now()->format('Y/m/d');
            $data['message'] = $event->items->keyBy('dvd_id')->keys()->join(PHP_EOL);
            $data['footer'] = Onejav::HOMEPAGE_URL . '/' . Carbon::now()->format('Y/m/d');

        } elseif ($event instanceof OnejavNewCompletedEvent) {
            $data['title'] = 'Total items: ' . $event->items->count();
            $data['message'] = 'State :' . $event->url->state_code;
            $data['footer'] = Onejav::NEW_URL . '?page=' . $event->url->data['current_page'] - 1;
        }

        Notification::route('slack', config('services.slack.notifications'))
            ->notify(new CrawlingCompletedNotification(OnejavService::SOURCE, $data));
    }

    public function subscribe($events)
    {
        $events->listen([
            OnejavDailyCompletedEvent::class,
            OnejavNewCompletedEvent::class,
        ], self::class . '@onejav');
    }
}
