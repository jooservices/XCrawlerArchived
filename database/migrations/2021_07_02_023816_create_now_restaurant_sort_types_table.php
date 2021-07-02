<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNowRestaurantSortTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('now_restaurant_sort_types', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique();

            $table->integer('display_order');
            $table->string('code');
            $table->string('name')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('now_restaurant_sort_types');
    }
}
