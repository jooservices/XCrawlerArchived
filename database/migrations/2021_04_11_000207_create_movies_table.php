<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name')->nullable();
            $table->string('cover')->nullable();
            $table->dateTime('sales_date')->nullable();
            $table->dateTime('release_date')->nullable();
            $table->string('content_id')->nullable();
            $table->string('dvd_id')->nullable()->index();
            $table->text('description')->nullable();
            $table->integer('time')->nullable();
            $table->string('director')->nullable();
            $table->string('studio')->nullable();
            $table->string('label')->nullable();
            $table->string('channel')->nullable();
            $table->string('series')->nullable();
            $table->text('gallery')->nullable();
            $table->string('sample')->nullable();
            $table->boolean('is_downloadable')->nullable();

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
        Schema::dropIfExists('movies');
    }
}
