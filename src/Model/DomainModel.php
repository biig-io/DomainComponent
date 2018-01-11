<?php

namespace Biig\Component\Domain\Model;

use Biig\Component\Domain\Event\DomainEvent;
use Biig\Component\Domain\Event\DomainEventDispatcher;

abstract class DomainModel implements ModelInterface
{
    /**
     * @var DomainEventDispatcher
     */
    private $dispatcher;

    /**
     * This is a patch to support events on instantiation. This is done because on construction the event dispatcher
     * is not available. Because Doctrine does not call the constructor on any object instantiation. But events are
     * unstacked as soon as the event dispatcher is available. (which must happen just after the construction).
     *
     * @var array
     */
    private $eventStack = [];

    public function setDispatcher(DomainEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        if (!empty($this->eventStack)) {
            while ($event = array_pop($this->eventStack)) {
                $this->dispatcher->dispatch($event['name'], $event['event']);
            }
        }
    }

    protected function dispatch(string $name, DomainEvent $event)
    {
        if (null === $this->dispatcher) {
            $this->eventStack[] = ['name' => $name, 'event' => $event];

            return;
        }

        $this->dispatcher->dispatch($name, $event);
    }
}
