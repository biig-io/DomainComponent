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

    /**
     * If true, it will be raised after doctrine flush.
     *
     * @var boolean
     */
    private $delayed;

    public function __construct($subject = null, $arguments = [], Event $originalEvent = null)
    {
        parent::__construct($subject, $arguments);
        $this->originalEvent = $originalEvent;
        $this->delayed = false;
    }

    /**
     * @return Event
     */
    public function getOriginalEvent(): ?Event
    {
        return $this->originalEvent;
    }

    public function isDelayed()
    {
        return $this->delayed;
    }

    /**
     * @internal
     */
    public function setDelayed()
    {
        $this->delayed = true;
    }
}
