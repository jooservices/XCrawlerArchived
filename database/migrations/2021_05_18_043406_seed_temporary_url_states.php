<?php

use App\Core\Database\Migration\Helpers\StateMigrate;
use App\Models\TemporaryUrl;

class SeedTemporaryUrlStates extends StateMigrate
{
    protected array $states = [
        [
            'reference_code' => TemporaryUrl::STATE_INIT,
            'entity' => TemporaryUrl::class,
            'state' => 'new',
        ],
        [
            'reference_code' => TemporaryUrl::STATE_COMPLETED,
            'entity' => TemporaryUrl::class,
            'state' => 'completed',
        ],
        [
            'reference_code' => TemporaryUrl::STATE_FAILED,
            'entity' => TemporaryUrl::class,
            'state' => 'failed',
        ],
    ];
}
