<?php

namespace App\Providers;

use App\Core\EventSourcing\Listeners\RecordedEventSubscriber;
use App\Core\EventSourcing\RecordedEvent;
use App\Listeners\CrawlingEventSubscriber;
use App\Listeners\Flickr\AlbumEventSubscriber;
use App\Listeners\Flickr\ContactEventSubscriber;
use App\Listeners\Flickr\DownloadItemSubscriber;
use App\Listeners\MovieEventSubscriber;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Models\FlickrDownloadItem;
use App\Models\Idol;
use App\Models\XCrawlerLog;
use App\Observers\Flickr\ContactObserver;
use App\Observers\Flickr\AlbumObserver;
use App\Observers\Flickr\DownloadItemObserver;
use App\Observers\IdolObserver;
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
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        MovieEventSubscriber::class,
        CrawlingEventSubscriber::class,
        DownloadItemSubscriber::class,

        // Flickr
        ContactEventSubscriber::class,
        AlbumEventSubscriber::class
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(RecordedEvent::class, RecordedEventSubscriber::class);
        XCrawlerLog::observe(XCrawlerLogObserver::class);
        Idol::observe(IdolObserver::class);

        // Flickr
        FlickrContact::observe(ContactObserver::class);
        FlickrAlbum::observe(AlbumObserver::class);
        FlickrDownloadItem::observe(DownloadItemObserver::class);
    }
}
