<?php

namespace Biig\Component\Domain\Rule;

use Biig\Component\Domain\Event\DomainEvent;

/**
 * Interface RuleInterface.
 *
 * You should never implements only this interface.
 * Use `DomainRuleInterface` or `DomainDelayedRuleInterface`.
 */
interface RuleInterface
{
    public function execute(DomainEvent $event);
}
