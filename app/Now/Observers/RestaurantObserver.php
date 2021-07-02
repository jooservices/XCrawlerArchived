<?php

namespace App\Now\Observers;

use App\Models\NowRestaurant;
use App\Now\Jobs\DeliverynowGetRestaurantDetail;

class RestaurantObserver
{
    public function created(NowRestaurant $restaurant)
    {
        DeliverynowGetRestaurantDetail::dispatch($restaurant);
    }
}
