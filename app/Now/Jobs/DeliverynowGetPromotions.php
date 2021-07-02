<?php

namespace App\Now\Jobs;

use App\Models\NowPromotion;
use App\Services\Client\XCrawlerJsonClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverynowGetPromotions implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        $client = app(XCrawlerJsonClient::class);
        $client->init();
        $response = $client->post('https://gappapi.deliverynow.vn/api/promotion/get_ids', [
            'city_id' => 217,
            'foody_service_id' => 1,
            'position' => [
                'latitude' => 10.785089,
                'longitude' => 106.694532
            ],
            'promotion_status' => 1,
            'sort_type' => 5,
        ]);

        if (!$response->isSuccessful()) {
            $this->fail();
            return;
        }

        $data = $response->getData()['promotion_ids'];
        foreach ($data as $promotion) {
            NowPromotion::updateOrCreate(['id' =>$promotion]);
        }
    }
}
