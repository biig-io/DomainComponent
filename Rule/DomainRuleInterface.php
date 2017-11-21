<?php

namespace Biig\Component\Domain\Rule;

use Biig\Component\Domain\Event\DomainEvent;

interface DomainRuleInterface
{
    /**
     * @param DomainEvent $event
     */
    public function execute(DomainEvent $event);

    /**
     * Returns an array of event or a string it listen on.
     *
     * @return array|string
     */
    public function on();
}
