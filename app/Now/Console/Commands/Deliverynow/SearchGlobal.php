<?php

namespace App\Now\Console\Commands\Deliverynow;

use App\Models\NowDistrict;
use App\Now\Jobs\DeliverynowSearchGlobal;
use Illuminate\Console\Command;

class SearchGlobal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'now:delivery-now-search-global';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function handle()
    {
        $this->output->progressStart(NowDistrict::count());
        foreach (NowDistrict::cursor() as $district) {
            $options = [
                'category_group' => 1,
                'city_id' => $district->city_id,
                'delivery_only' => true,
                'foody_services' => [
                    1
                ],
                'full_restaurant_ids' => true,
                'keyword' => '',
                'position' => [
                    'latitude' => 10.785089,
                    'longitude' => 106.694532
                ],
                'sort_type' => 8,
                'district_ids' => [$district->id]
            ];
            DeliverynowSearchGlobal::dispatch($options);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }
}
