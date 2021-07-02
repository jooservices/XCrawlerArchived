<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNowRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('now_restaurants', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique();

            $table->string('address')->nullable()->index();
            $table->unsignedBigInteger('brand_id')->nullable();

            $table->string('name')->nullable()->index();
            $table->string('name_en')->nullable()->index();

            $table->unsignedBigInteger('parent_category_id')->nullable()->index();

            $table->integer('price_from')->nullable();
            $table->integer('price_to')->nullable();

            $table->float('rating')->nullable();
            $table->float('total_review')->nullable();
            $table->string('restaurant_url')->nullable()->index();

            $table->unsignedBigInteger('city_id')->nullable()->index();
            $table->foreign('city_id')->on('now_cities')->references('id');

            $table->unsignedBigInteger('district_id')->nullable()->index();
            $table->foreign('district_id')->on('now_districts')->references('id');

            $table->unsignedBigInteger('delivery_id')->nullable()->index();

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
        Schema::dropIfExists('now_restaurants');
    }
}
