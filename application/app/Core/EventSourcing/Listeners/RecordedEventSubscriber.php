<?php

namespace App\Core\EventSourcing\Listeners;

use App\Core\EventSourcing\EventManager;
use App\Core\EventSourcing\RecordedEvent;

class RecordedEventSubscriber
{
    private EventManager $eventManager;

    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function handle(RecordedEvent $event)
    {
        $this->eventManager
            ->setEvent(class_basename($event))
            ->setCategory($event->getCategory())
            ->setInstance($event->getAggregate())
            ->setAttributes($event->getPayload())
            ->store();
    }
}
