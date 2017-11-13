<?php

namespace Biig\Component\Domain\Model\Instantiator;

interface DomainModelInstantiatorInterface
{
    /**
     * Notice: this method is not type hinted to be compatible with doctrine instantiator.
     *
     * @param string $className
     * @return object
     */
    public function instantiate($className);
}
