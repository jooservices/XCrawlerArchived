<?php

namespace App\Now\Providers;

use App\Models\NowRestaurant;
use App\Now\Observers\RestaurantObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class NowServiceProvider extends ServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected array $subscribe = [

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        NowRestaurant::observe(RestaurantObserver::class);

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    public function register()
    {
        parent::register();


    }
}
