<?php

namespace App\Events\Jav;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OnejavDailyCompletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Collection $items;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Collection $items)
    {
        $this->items = $items;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
