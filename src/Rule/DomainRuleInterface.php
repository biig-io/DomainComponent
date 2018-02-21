<?php

namespace Biig\Component\Domain\Rule;

interface DomainRuleInterface extends RuleInterface
{
    /**
     * Returns an array of event or a string it listen on.
     *
     * @return array|string
     */
    public function on();
}
