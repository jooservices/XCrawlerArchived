<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropTableXCityIdolPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('x_city_idol_pages', 'DEPRECATED_x_city_idol_pages');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('DEPRECATED_x_city_idol_pages', 'x_city_idol_pages');
    }
}
