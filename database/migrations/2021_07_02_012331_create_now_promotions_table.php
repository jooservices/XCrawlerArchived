<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNowPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('now_promotions', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique();
            $table->integer('discount_amount')->nullable();
            $table->integer('max_discount_amount')->nullable();
            $table->integer('min_order_amount')->nullable();

            $table->integer('discount_on_type')->nullable();
            $table->integer('discount_type')->nullable();
            $table->integer('discount_value_type')->nullable();
            $table->dateTime('expired')->nullable();
            $table->string('home_title')->nullable();
            $table->string('promo_code')->nullable();
            $table->integer('promotion_type')->nullable();

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
        Schema::dropIfExists('now_promtions');
    }
}
