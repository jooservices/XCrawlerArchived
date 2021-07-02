<?php

namespace App\Now\Jobs;

use App\Models\NowCollection;
use App\Services\Client\XCrawlerJsonClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverynowGetCollections implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        $client = app(XCrawlerJsonClient::class);
        $client->init();
        $response = $client->get('https://gappapi.deliverynow.vn/api/collection/get_ids', [
            'city_id' => 217,
            'foody_service_id' => 1,
            'visibility' => 1,
        ]);

        if (!$response->isSuccessful()) {
            $this->fail();
            return;
        }

        $response = $client->post('https://gappapi.deliverynow.vn/api/collection/get_infos', [
            'ids' => $response->getData()['ids']
        ]);

        if (!$response->isSuccessful()) {
            $this->fail();
            return;
        }

        $data = $response->getData()['collections'];

        foreach ($data as $collection) {
            NowCollection::updateOrCreate([
                'id' => $collection ['id']
            ], [
                'name' => $collection['name'],
                'description' => $collection['description'],
                'url' => $collection['url'],
                'url_rewrite_name' => $collection['url_rewrite_name'],
            ]);
        }

    }
}
