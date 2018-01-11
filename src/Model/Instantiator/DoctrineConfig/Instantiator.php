<?php

namespace Biig\Component\Domain\Model\Instantiator\DoctrineConfig;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\Instantiator\Instantiator as BaseInstantiator;
use Doctrine\Instantiator\InstantiatorInterface;

/**
 * Decorates the Doctrine standard instantiator to add new behavior.
 */
class Instantiator extends BaseInstantiator implements InstantiatorInterface
{
    public function __construct(DomainEventDispatcher $dispatcher)
    {
        parent::__construct($dispatcher);
    }

    public function instantiate($object)
    {
        $this->injectDispatcher($object);

        return $object;
    }
}
