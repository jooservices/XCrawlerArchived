<?php

namespace App\Core\Database\Migration\Helpers;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StateMigrate extends Migration
{
    protected array $states;
    protected ?string $foreignKey = null;

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

        if ($this->foreignKey)
        {
            Schema::table($this->foreignKey, function (Blueprint $table) {
                $table->foreign('state_code')->references('reference_code')->on('states');
            });
        }
    }
}
