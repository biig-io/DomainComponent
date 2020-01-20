<?php

namespace Biig\Component\Domain\Event;

use Biig\Component\Domain\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\GenericEvent;

class DomainEvent extends GenericEvent
{
    /**
     * @var \Symfony\Component\EventDispatcher\Event|\Symfony\Contracts\EventDispatcher\Event|null
     */
    private $originalEvent;

    /**
     * If true, it will be raised after doctrine flush.
     *
     * @var bool
     */
    private $delayed;

    public function __construct($subject = null, $arguments = [], /* Event */ $originalEvent = null)
    {
        // BC layer for Symfony 4.3
        if (!\is_null($originalEvent) &&
            !((class_exists(\Symfony\Component\EventDispatcher\Event::class) && $originalEvent instanceof \Symfony\Component\EventDispatcher\Event)
            || (class_exists(\Symfony\Contracts\EventDispatcher\Event::class) && $originalEvent instanceof \Symfony\Contracts\EventDispatcher\Event)
            )
        ) {
            throw new InvalidArgumentException('The orignal event must be an instance of Symfony Events');
        }

        parent::__construct($subject, $arguments);
        $this->originalEvent = $originalEvent;
        $this->delayed = false;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\Event|\Symfony\Contracts\EventDispatcher\Event|null
     */
    public function getOriginalEvent()
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
