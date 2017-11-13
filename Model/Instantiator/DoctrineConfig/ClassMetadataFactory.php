<?php

namespace Biig\Component\Domain\Model\Instantiator\DoctrineConfig;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Doctrine\ORM\Mapping\ClassMetadataFactory as BaseClassMetadataFactory;

final class ClassMetadataFactory extends BaseClassMetadataFactory
{
    private $dispatcher;

    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($className)
    {
        return new ClassMetadataDecorator(parent::getMetadataFor($className), new Instantiator($this->dispacher));
    }

    /**
     * @param DomainEventDispatcher $dispatcher
     */
    public function setDispatcher(DomainEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
