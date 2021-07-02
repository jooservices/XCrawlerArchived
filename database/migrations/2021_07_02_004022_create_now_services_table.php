<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNowServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('now_services', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique();
            $table->foreignId('country_id')->constrained('now_countries');

            $table->string('name')->index();
            $table->string('call_center');
            $table->string('code');
            $table->string('url')->index();

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
        Schema::dropIfExists('now_services');
    }
}
