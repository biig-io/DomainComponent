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
     *
     * @param string   $eventName
     * @param callable $listener
     */
    public function __construct(string $eventName, callable $listener)
    {
        $this->eventName = $eventName;
        $this->listener = $listener;
        $this->eventStack = [];
    }

    /**
     * @param DomainEvent $event
     *
     * @throws InvalidDomainEvent
     */
    public function occur(DomainEvent $event)
    {
        $subject = $event->getSubject();

        if (!is_object($subject) || !$subject instanceof ModelInterface) {
            throw new InvalidDomainEvent(
                sprintf(
                    'The event "%s" is invalid because no domain model subject is specified while the event must be dispatched after persist.',
                    get_class($event)
                )
            );
        }

        $this->eventStack[] = $event;
    }

    /**
     * Execute the listener on the events that already occurred.
     *
     * @param ModelInterface $model
     */
    public function process(ModelInterface $model)
    {
        $stack = $this->eventStack;
        foreach ($stack as $key => $event) {
            if (spl_object_hash($event->getSubject()) === spl_object_hash($model)) {
                \call_user_func($this->listener, $event);
                unset($this->eventStack[$key]);
            }
        }
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @param ModelInterface $model
     *
     * @return bool
     */
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
