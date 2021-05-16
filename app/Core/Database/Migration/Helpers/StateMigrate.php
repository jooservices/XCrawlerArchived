<?php

namespace App\Core\Database\Migration\Helpers;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StateMigrate extends Migration
{
    protected array $states;

    public function up()
    {
        $now = Carbon::now();
        foreach ($this->states as $state) {
            DB::table('states')->insert([
                'reference_code' => $state['reference_code'],
                'entity' => $state['entity'],
                'state' => $state['state'],
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }
}
