<?php

namespace Biig\Component\Domain\Model;

use Biig\Component\Domain\Event\DomainEvent;
use Biig\Component\Domain\Event\DomainEventDispatcherInterface;

trait DomainModelTrait
{
    /**
     * @var DomainEventDispatcherInterface
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

    public function setDispatcher(DomainEventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        if (!empty($this->eventStack)) {
            while ($event = array_pop($this->eventStack)) {
                if (isset($event['name'])) {
                    $this->dispatcher->dispatch($event['event'], $event['name']);
                } else {
                    $this->dispatcher->dispatch($event['event']);
                }
            }
        }
    }

    protected function dispatch(DomainEvent $event, string $name = null)
    {
        if (null === $this->dispatcher) {
            $eventToStack['event'] = $event;
            if (!\is_null($name)) {
                $eventToStack['name'] = $name;
            }
            $this->eventStack[] = $eventToStack;

            return;
        }

        $this->dispatcher->dispatch($event, $name);
    }
}
