<?php

use App\Models\XCityIdol;
use App\Services\TemporaryUrlService;
use Illuminate\Database\Migrations\Migration;

class MigrateXCityIdols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Migrate items event already fetched cos we missed field in fillable
         */
        $service = app(TemporaryUrlService::class);
        foreach (XCityIdol::cursor() as $idol) {
            $service->create(XCityIdol::HOMEPAGE_URL . $idol->url, 'xcity.idol');
            $idol->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
