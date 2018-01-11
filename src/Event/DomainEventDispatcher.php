<?php

namespace Biig\Component\Domain\Event;

use Biig\Component\Domain\Rule\DomainRuleInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DomainEventDispatcher extends EventDispatcher
{
    /**
     * @param DomainRuleInterface $rule
     */
    public function addRule(DomainRuleInterface $rule)
    {
        $events = $rule->on();

        if (!is_array($events)) {
            $events = [$events];
        }

        foreach ($events as $event) {
            $this->addListener($event, [$rule, 'execute']);
        }
    }
}
