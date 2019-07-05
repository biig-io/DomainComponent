<?php

namespace Biig\Component\Domain\Tests\Symfony\DependencyInjection;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Integration\Symfony\DependencyInjection\EntityManagerConfigurator;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadataFactory;
use Doctrine\Bundle\DoctrineBundle\ManagerConfigurator;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class EntityManagerConfiguratorTest extends TestCase
{
    public function testItInsertsTheDispatcher()
    {
        $entityManager = $this->prophesize(EntityManager::class);
        $originalConfigurator = $this->prophesize(ManagerConfigurator::class)->reveal();
        $factory = new ClassMetadataFactory();

        $entityManager->getMetadataFactory()->willReturn($factory);

        $configurator = new EntityManagerConfigurator($originalConfigurator, new DomainEventDispatcher());
        $configurator->configure($entityManager->reveal());

        $ref = new \ReflectionObject($factory);
        $property = $ref->getProperty('dispatcher');
        $property->setAccessible(true);
        $this->assertNotNull($property->getValue($factory));
    }
}
