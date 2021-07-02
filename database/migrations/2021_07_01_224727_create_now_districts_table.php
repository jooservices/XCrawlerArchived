<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNowDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('now_districts', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique();
            $table->foreignId('city_id')->constrained('now_cities');

            $table->string('name')->index();
            $table->string('url_rewrite_name')->index();
            $table->float('latitude');
            $table->float('longitude');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('now_districts');
    }
}
