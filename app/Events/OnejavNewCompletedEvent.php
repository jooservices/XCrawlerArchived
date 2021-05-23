<?php

namespace App\Events;

use App\Models\TemporaryUrl;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OnejavNewCompletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TemporaryUrl $url;
    public Collection $items;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TemporaryUrl $temporaryUrl, Collection $items)
    {
        $this->url = $temporaryUrl;
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
