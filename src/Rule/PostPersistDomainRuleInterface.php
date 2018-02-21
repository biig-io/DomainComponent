<?php

namespace Biig\Component\Domain\Rule;

/**
 * Interface DomainDelayedRuleInterface.
 *
 * This interface act like DomainRuleInterface but delay the behavior after the persist of
 * the data of the domain object. This may be useful for emailing or any post persist thing.
 */
interface PostPersistDomainRuleInterface extends RuleInterface
{
    /**
     * Returns an array of event or a string it listen on.
     *
     * @return array|string
     */
    public function after();
}
