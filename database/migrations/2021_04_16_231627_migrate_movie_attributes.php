<?php

use App\Models\Idol;
use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MigrateMovieAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = Carbon::now();
        foreach (DB::table('movie_attributes')->cursor() as $tagAttribute) {
            switch ($tagAttribute->model_type) {
                case Tag::class:
                    DB::table('tag_movie')->insert([
                        'tag_id' => $tagAttribute->model_id,
                        'movie_id' => $tagAttribute->movie_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    break;
                case Idol::class:
                    DB::table('idol_movie')->insert([
                        'idol_id' => $tagAttribute->model_id,
                        'movie_id' => $tagAttribute->movie_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    break;
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
