<?php

namespace Biig\Component\Domain\Model\Instantiator\DoctrineConfig;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Doctrine\ORM\EntityManager;

class EntityManagerConfigurator
{
    private $dispatcher;

    public function __construct(DomainEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function configure (EntityManager $entityManager)
    {
        $metadataFactory = $entityManager->getMetadataFactory();

        if ($metadataFactory instanceof ClassMetadataFactory) {
            $metadataFactory->setDispatcher($this->dispatcher);
        }
    }
}
