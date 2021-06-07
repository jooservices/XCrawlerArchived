<?php

use App\Models\Idol;
use App\Models\MovieAttribute;
use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedMovieAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (DB::table('movies')->cursor() as $movie) {
            $onejavs = DB::table('onejav')->where(['dvd_id' => $movie->dvd_id])->get();
            foreach ($onejavs as $onejav) {
                if ($tags = json_decode($onejav->tags)) {
                    foreach ($tags as $tag) {
                        if (!$tag = Tag::firstOrCreate(['name' => $tag])) {
                            continue;
                        }

                        MovieAttribute::firstOrCreate([
                            'movie_id' => $movie->id,
                            'model_id' => $tag->id,
                            'model_type' => Tag::class,
                        ]);
                    }
                };

                if ($actresses = json_decode($onejav->actresses)) {
                    foreach ($actresses as $actress) {
                        if (!$actress = Idol::firstOrCreate(['name' => $actress])) {
                            continue;
                        }

                        MovieAttribute::firstOrCreate([
                            'movie_id' => $movie->id,
                            'model_id' => $actress->id,
                            'model_type' => Idol::class,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
