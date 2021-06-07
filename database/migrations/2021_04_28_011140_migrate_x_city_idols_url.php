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
            $url = str_replace('https://xxx.xcity.jp/idol/', '', $idol->url);
            if (XCityIdol::where(['url' => $url])->where('id', '<>', $idol->id)->exists()) {
                $idol->delete();
            }
            $idol->url = $url;
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
