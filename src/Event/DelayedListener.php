<?php

namespace Biig\Component\Domain\Event;

use Biig\Component\Domain\Exception\InvalidDomainEvent;
use Biig\Component\Domain\Model\ModelInterface;

class DelayedListener
{
    /**
     * @var string
     */
    private $eventName;

    /**
     * @var callable
     */
    private $listener;

    /**
     * @var DomainEvent[]
     */
    private $eventStack;

    /**
     * DelayedEvent constructor.
     */
    public function __construct(string $eventName, callable $listener)
    {
        $this->eventName = $eventName;
        $this->listener = $listener;
        $this->eventStack = [];
    }

    /**
     * @throws InvalidDomainEvent
     */
    public function occur(DomainEvent $event)
    {
        $event->setDelayed();
        $subject = $event->getSubject();

        if (!is_object($subject) || !$subject instanceof ModelInterface) {
            throw new InvalidDomainEvent(sprintf('The event "%s" is invalid because no domain model subject is specified while the event must be dispatched after persist.', get_class($event)));
        }

        $this->eventStack[] = $event;
    }

    /**
     * Execute the listener on the events that already occurred.
     */
    public function process(ModelInterface $model)
    {
        $tmpStack = $this->eventStack;
        $this->eventStack = [];
        $eventStack = [];

        foreach ($tmpStack as $event) {
            if (spl_object_hash($event->getSubject()) === spl_object_hash($model)) {
                $eventStack[] = $event;
                continue;
            }
            $this->eventStack[] = $event;
        }

        foreach ($eventStack as $key => $event) {
            \call_user_func($this->listener, $event);
        }
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function shouldOccur(ModelInterface $model): bool
    {
        if (empty($this->eventStack)) {
            return false;
        }

        foreach ($this->eventStack as $event) {
            if (spl_object_hash($event->getSubject()) === spl_object_hash($model)) {
                return true;
            }
        }

        return false;
    }
}
