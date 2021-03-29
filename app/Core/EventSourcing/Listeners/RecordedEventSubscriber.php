<?php

namespace App\Core\EventSourcing\Listeners;

use App\Core\EventSourcing\EventManager;
use App\Core\EventSourcing\RecordedEventInterface;

class RecordedEventSubscriber
{
    private EventManager $eventManager;

    /**
     * @param \App\Core\EventSourcing\EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @param \App\Core\EventSourcing\RecordedEventInterface $event
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(RecordedEventInterface $event): void
    {
        $this->eventManager
            ->setEvent(class_basename($event))
            ->setCategory($event->getCategory())
            ->setInstance($event->getAggregate())
            ->setAttributes($event->getPayload())
            ->store();
    }
}
