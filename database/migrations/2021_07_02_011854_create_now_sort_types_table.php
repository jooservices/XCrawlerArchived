<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNowSortTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('now_sort_types', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique();

            $table->string('code');
            $table->integer('constant_id');
            $table->integer('display_order');
            $table->integer('is_required_location');
            $table->string('name')->index();
            $table->integer('status');
            $table->integer('type');

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
        Schema::dropIfExists('now_sort_types');
    }
}
