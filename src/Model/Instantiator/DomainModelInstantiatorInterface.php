<?php

namespace Biig\Component\Domain\Model\Instantiator;

interface DomainModelInstantiatorInterface
{
    /**
     * This method is not type hinted to be compatible with doctrine instantiator (InstantiatorInterface).
     * We do not inherit from the InstantiatorInterface to allow the usage of this component without doctrine.
     * This is also the reason to not have arguments in this method.
     *
     *
     * @param string $className
     *
     * @return object
     */
    public function instantiate($className);

    /**
     * @param string $className
     * @param array  ...$args
     *
     * @return object
     */
    public function instantiateWithArguments(string $className, ...$args);
}
