<?php

namespace App\Providers;

use App\Core\EventSourcing\Listeners\RecordedEventSubscriber;
use App\Core\EventSourcing\RecordedEvent;
use App\Models\User;
use App\Models\XCrawlerLog;
use App\Observers\UserObserver;
use App\Observers\XCrawlerLogObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(RecordedEvent::class, RecordedEventSubscriber::class);

        User::observe(UserObserver::class);
        XCrawlerLog::observe(XCrawlerLogObserver::class);
    }
}
