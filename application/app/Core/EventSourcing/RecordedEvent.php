<?php

namespace App\Core\EventSourcing;

use Illuminate\Database\Eloquent\Model;

interface RecordedEvent
{
    public function getCategory(): string;

    public function getPayload(): array;

    /**
     * This is not necessarily good in the long run as it limits us to Eloquent only
     * @return Model
     */
    public function getAggregate(): Model;
}
