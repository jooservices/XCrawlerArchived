<?php

namespace App\Flickr\Providers;

use App\Flickr\Interfaces\FlickrClientInterface;
use App\Flickr\Listeners\AlbumEventSubscriber;
use App\Flickr\Listeners\ContactEventSubscriber;
use App\Flickr\Listeners\DownloadItemSubscriber;
use App\Flickr\Observers\AlbumObserver;
use App\Flickr\Observers\ContactObserver;
use App\Flickr\Observers\DownloadItemObserver;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Models\FlickrDownloadItem;
use App\Models\Integration;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\FlickrClientResponse;
use App\Services\Flickr\PhpFlickr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Jooservices\PhpFlickr\FlickrException;
use OAuth\Common\Storage\Memory;
use OAuth\OAuth1\Token\StdOAuth1Token;

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

    public function register()
    {
        parent::register();

        $this->app->bind(FlickrClientInterface::class, function () {
            if (!$integration = Integration::forService('flickr')->first()) {
                throw new FlickrException('There is no integration yet');
            }

            $storage = new Memory();
            $token = new StdOAuth1Token();
            $token->setAccessToken($integration->token);
            $token->setAccessTokenSecret($integration->token_secret);
            $storage->storeAccessToken('Flickr', $token);

            $instance = new PhpFlickr(config('services.flickr.client_id'), config('services.flickr.client_secret'));
            $instance->setOauthStorage($storage);

            return $instance;
        });

        $this->app->bind(ResponseInterface::class, FlickrClientResponse::class);
    }
}
