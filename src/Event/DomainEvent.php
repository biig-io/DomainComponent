<?php

namespace Biig\Component\Domain\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

class DomainEvent extends GenericEvent
{
    /**
     * @var Event
     */
    private $originalEvent;

    public function __construct($subject = null, $arguments = [], Event $originalEvent = null)
    {
        parent::__construct($subject, $arguments);
        $this->originalEvent = $originalEvent;
    }

    /**
     * @return Event
     */
    public function getOriginalEvent(): ?Event
    {
        return $this->originalEvent;
    }
}
