<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNowCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('now_countries', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique();

            $table->string('name')->index();
            $table->string('language_code')->index();
            $table->string('api_url');
            $table->string('two_letter_iso_code');

            $table->timestamps();
            $table->softDeletes();
        });

        \Illuminate\Support\Facades\DB::table('now_countries')
            ->insert([
                'id' => 86,
                'name' => 'Vietnam',
                'language_code' => 'VN',
                'api_url' => 'https://api.foody.vn/api',
                'two_letter_iso_code' => 'VN'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('now_countries');
    }
}
