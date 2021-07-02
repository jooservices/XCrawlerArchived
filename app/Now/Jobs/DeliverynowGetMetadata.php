<?php

namespace App\Now\Jobs;

use App\Models\NowCities;
use App\Models\NowDistrict;
use App\Models\NowExtraFee;
use App\Models\NowRestaurantSortType;
use App\Models\NowServices;
use App\Models\NowShipperTip;
use App\Services\Client\XCrawlerJsonClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverynowGetMetadata implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        $client = app(XCrawlerJsonClient::class);
        $client->init();
        $response = $client->get('https://gappapi.deliverynow.vn/api/meta/get_metadata');

        if (!$response->isSuccessful()) {
            $this->fail();
            return;
        }

        $data = $response->getData();
        $cities = collect($data['country']['cities']);

        $cities->each(function ($city) use ($data) {
            NowCities::updateOrCreate([
                'id' => $city['id'],
            ], [
                'country_id' => $data['country']['id'],
                'name' => $city['name'],
                'latitude' => $city['latitude'],
                'longitude' => $city['longitude'],
                'url_rewrite_name' => $city['url_rewrite_name']
            ]);
            foreach ($city['districts'] as $district) {
                NowDistrict::updateOrCreate([
                    'id' => $district['district_id'],
                    'city_id' => $city['id'],
                ], [

                    'name' => $district['name'],
                    'latitude' => $district['latitude'],
                    'longitude' => $district['longitude'],
                    'url_rewrite_name' => $district['url_rewrite_name'],
                ]);
            }
        });

        foreach ($data['country']['now_services'] as $service) {
            NowServices::updateOrCreate([
                'id' => $service['id'],
            ], [
                'country_id' => $data['country']['id'],
                'name' => $service['name'],
                'call_center' => $service['call_center'],
                'code' => $service['code'],
                'url' => $service['url'],
            ]);
        }

        foreach ($data['country']['order_extra_fees']['customer_parking_fee_options'] as $value) {
            NowExtraFee::firstOrCreate([
                'value' => $value
            ]);
        }

        foreach ($data['country']['order_extra_fees']['shipper_tip_options'] as $value) {
            NowShipperTip::firstOrCreate([
                'value' => $value
            ]);
        }

        foreach ($data['country']['restaurant_sort_type'] as $restaurantSortType) {
            NowRestaurantSortType::updateOrCreate([
                'id' => $restaurantSortType['id']
            ], [
                'display_order' => $restaurantSortType['display_order'],
                'code' => $restaurantSortType['code'],
                'name' => $restaurantSortType['name'],
            ]);
        }
    }
}
