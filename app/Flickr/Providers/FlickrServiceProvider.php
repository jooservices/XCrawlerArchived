<?php

namespace App\Flickr\Providers;

use App\Flickr\Listeners\AlbumEventSubscriber;
use App\Flickr\Listeners\ContactEventSubscriber;
use App\Flickr\Listeners\DownloadItemSubscriber;
use App\Flickr\Observers\AlbumObserver;
use App\Flickr\Observers\ContactObserver;
use App\Flickr\Observers\DownloadItemObserver;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Models\FlickrDownloadItem;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class FlickrServiceProvider extends ServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected array $subscribe = [
        ContactEventSubscriber::class,
        AlbumEventSubscriber::class,
        DownloadItemSubscriber::class
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        FlickrContact::observe(ContactObserver::class);
        FlickrAlbum::observe(AlbumObserver::class);
        FlickrDownloadItem::observe(DownloadItemObserver::class);

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }
}
