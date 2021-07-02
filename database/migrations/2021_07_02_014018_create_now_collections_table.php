<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNowCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('now_collections', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique();

            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->string('url');
            $table->string('url_rewrite_name');

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
        Schema::dropIfExists('now_collections');
    }
}
