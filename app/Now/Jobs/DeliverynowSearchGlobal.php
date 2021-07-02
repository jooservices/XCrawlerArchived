<?php

namespace App\Now\Jobs;

use App\Models\NowRestaurant;
use App\Services\Client\XCrawlerJsonClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverynowSearchGlobal implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public array $options)
    {
    }

    public function handle()
    {
        $client = app(XCrawlerJsonClient::class);
        $client->init();

        $response = $client->post('https://gappapi.deliverynow.vn/api/delivery/search_global', $this->options);

        if (!$response->isSuccessful()) {
            return;
        }

        $data = $response->getData();

        foreach ($data['search_result'] as $result) {
            foreach ($result['restaurant_ids'] as $restaurantId) {
                NowRestaurant::updateOrCreate([
                    'id' => $restaurantId
                ]);
            }
        }
    }
}
