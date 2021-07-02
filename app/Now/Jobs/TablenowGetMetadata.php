<?php

namespace App\Now\Jobs;

use App\Models\NowBookingCategory;
use App\Models\NowCuisine;
use App\Models\NowSortType;
use App\Services\Client\XCrawlerJsonClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TablenowGetMetadata implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        $client = app(XCrawlerJsonClient::class);
        $client->init();
        $response = $client->get('https://gappapi.tablenow.vn/api/metadata/get_metadata');

        if (!$response->isSuccessful()) {
            $this->fail();
            return;
        }

        $data = $response->getData();
        $bookingCategories = collect($data['metadata']['booking_categories']);

        $bookingCategories->each(function ($category) use ($data) {
            NowBookingCategory::updateOrCreate([
                'id' => $category['id'],
                'parent_id' => null,
                'name' => $category['name'],
            ]);
        });

        foreach ($data['metadata']['cuisine'] as $cuisine) {
            NowCuisine::updateOrCreate([
                'id' => $cuisine['id'],
                'parent_id' => null,
                'name' => $cuisine['name'],
            ]);
        }

        foreach ($data['metadata']['sort_type'] as $sortType) {
            NowSortType::updateOrCreate($sortType);
        }
    }
}
