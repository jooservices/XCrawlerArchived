<?php

namespace App\Listeners;

use App\Events\Jav\OnejavDailyCompletedEvent;
use App\Events\OnejavNewCompletedEvent;
use App\Models\Onejav;
use Carbon\Carbon;

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
    }

    public function subscribe($events)
    {
        $events->listen([
            OnejavDailyCompletedEvent::class,
            OnejavNewCompletedEvent::class,
        ], self::class . '@onejav');
    }
}
