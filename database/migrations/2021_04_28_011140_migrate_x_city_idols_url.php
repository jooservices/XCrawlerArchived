<?php

use App\Models\XCityIdol;
use Illuminate\Database\Migrations\Migration;

class MigrateXCityIdolsUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (XCityIdol::cursor() as $idol) {
            $idol->url = str_replace('https://xxx.xcity.jp/idol/', '', $idol->url);
            $idol->update();
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
