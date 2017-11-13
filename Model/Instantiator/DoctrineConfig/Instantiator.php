<?php

namespace Biig\Component\Domain\Model\Instantiator\DoctrineConfig;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Doctrine\Instantiator\InstantiatorInterface;
use Biig\Component\Domain\Model\Instantiator\Instantiator as BaseInstantiator;

/**
 * Decorates the Doctrine standard instantiator to add new behavior.
 */
class Instantiator extends BaseInstantiator implements InstantiatorInterface
{
    /**
     * @var InstantiatorInterface
     */
    private $instantiator;

    public function __construct(DomainEventDispatcher $dispatcher, InstantiatorInterface $instantiator = null)
    {
        parent::__construct($dispatcher);
        $this->instantiator = $instantiator;
    }

    public function instantiate($className)
    {
        $object = $this->instantiator->instantiate($className);
        $this->injectDispatcher($object);

        return $object;
    }
}
