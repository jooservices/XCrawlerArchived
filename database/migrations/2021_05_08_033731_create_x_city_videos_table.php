<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXCityVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_city_videos', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->string('cover')->nullable();
            $table->dateTime('sales_date')->nullable();
            $table->dateTime('release_date')->nullable();
            $table->string('item_number')->unique();
            $table->string('dvd_id')->unique();
            $table->json('tags')->nullable();
            $table->json('actresses')->nullable();
            $table->text('description')->nullable();
            $table->integer('time')->nullable();
            $table->string('director')->nullable();
            $table->string('marker')->nullable();
            $table->string('studio')->nullable();
            $table->string('label')->nullable();
            $table->string('channel')->nullable();
            $table->string('series')->nullable();
            $table->text('gallery')->nullable();
            $table->string('sample')->nullable();
            $table->integer('favorite')->nullable();

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
        Schema::dropIfExists('x_city_videos');
    }
}
