<?php

namespace App\Models\Traits;

use App\Models\Event;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasEvents
{
    /**
     * A Model has many events.
     *
     * @return MorphMany|Event
     */
    public function events()
    {
        return $this->morphMany(Event::class, 'model')->latest('id');
    }

    /**
     * Determine whether this Aggregate has ever recorded given event
     *
     * @param object|string $event Domain event instance or its FQCN
     * @param callable|null $where WHERE callback to apply on the query: fn ($query) => ...
     * @return bool
     */
    public function hasEvent($event, callable $where = null): bool
    {
        // TODO this might require some fancy SQL optimizations in the long run
        return $this->events()
            ->when($where, $where)
            ->forEvent($event)
            ->exists();
    }
}
