<?php

namespace Biig\Component\Domain\Integration\Symfony\DependencyInjection;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadataFactory;
use Doctrine\Bundle\DoctrineBundle\ManagerConfigurator;
use Doctrine\ORM\EntityManager;

/**
 * @internal
 */
class EntityManagerConfigurator
{
    /**
     * @var ManagerConfigurator
     */
    private $originalConfigurator;

    /**
     * @var DomainEventDispatcher
     */
    private $dispatcher;

    public function __construct(ManagerConfigurator $configurator, DomainEventDispatcher $dispatcher)
    {
        $this->originalConfigurator = $configurator;
        $this->dispatcher = $dispatcher;
    }

    public function configure(EntityManager $entityManager)
    {
        $this->originalConfigurator->configure($entityManager);
        $metadataFactory = $entityManager->getMetadataFactory();

        if ($metadataFactory instanceof ClassMetadataFactory) {
            $metadataFactory->setDispatcher($this->dispatcher);
        }
    }
}
