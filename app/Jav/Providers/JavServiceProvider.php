<?php

namespace App\Jav\Providers;

use App\Jav\Listeners\CrawlingEventSubscriber;
use App\Jav\Listeners\MovieEventSubscriber;
use App\Jav\Observers\IdolObserver;
use App\Jav\Observers\XCrawlerLogObserver;
use App\Models\Idol;
use App\Models\XCrawlerLog;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class JavServiceProvider extends ServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected array $subscribe = [
        MovieEventSubscriber::class,
        CrawlingEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        XCrawlerLog::observe(XCrawlerLogObserver::class);
        Idol::observe(IdolObserver::class);

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }
}
