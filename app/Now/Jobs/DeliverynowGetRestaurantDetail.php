<?php

namespace App\Now\Jobs;

use App\Models\NowPromotion;
use App\Models\NowRestaurant;
use App\Services\Client\XCrawlerJsonClient;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverynowGetRestaurantDetail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public NowRestaurant $restaurant)
    {

    }

    public function handle()
    {
        $client = app(XCrawlerJsonClient::class);
        $client->init();
        $response = $client->get('https://gappapi.deliverynow.vn/api/delivery/get_detail', [
            'id_type' => 2,
            'request_id' => $this->restaurant->id,
        ]);

        if (!$response->isSuccessful()) {
            $this->restaurant->delete();
            return;
        }

        $data = $response->getData();
        $delivery = $data['delivery_detail'];
        $this->restaurant->update([
            'address' => $delivery['address'],
            'name' => $delivery['name'],
            'name_en' => $delivery['name_en'],
            'parent_category_id' => $delivery['parent_category_id'],
            'price_from' => $delivery['price_range']['min_price'],
            'price_to' => $delivery['price_range']['max_price'],
            'rating' => $delivery['rating']['avg'],
            'total_review' => $delivery['rating']['total_review'],
            'restaurant_url' => $delivery['restaurant_url'],
            'city_id' => $delivery['city_id'],
            'district_id' => $delivery['district_id'],
            'delivery_id' => $delivery['delivery_id'],
        ]);

        // Promotions
        $promotions = $delivery['delivery']['promotions'] ?? [];
        foreach ($promotions as $promotion) {
            $promotion = NowPromotion::updateOrCreate([
                'id' => $promotion['id'],
                'discount_amount' => $promotion['discount_amount'],
                'max_discount_amount' => $promotion['max_discount_amount'],
                'min_order_amount' => $promotion['min_order_amount'],
                'discount_on_type' => $promotion['discount_on_type'],
                'discount_type' => $promotion['discount_type'],
                'discount_value_type' => $promotion['discount_value_type'],
                'expired' => Carbon::createFromFormat('d/m/Y G:i', $promotion['expired']),
                'home_title' => $promotion['home_title'],
                'promotion_type' => $promotion['promotion_type'],
            ]);

            $this->restaurant->promotions()->syncWithoutDetaching([$promotion->id]);
        }
    }
}
