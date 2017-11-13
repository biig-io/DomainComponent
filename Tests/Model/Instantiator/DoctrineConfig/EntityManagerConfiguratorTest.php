<?php

namespace Biig\Component\Domain\Tests\Model\Instantiator\DoctrineConfig;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadataFactory;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\EntityManagerConfigurator;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class EntityManagerConfiguratorTest extends TestCase
{
    public function testItInsertTheDispacher()
    {
        $entityManager = $this->prophesize(EntityManager::class);
        $dispacher = $this->prophesize(DomainEventDispatcher::class)->reveal();
        $factory = new ClassMetadataFactory();

        $entityManager->getMetadataFactory()->willReturn($factory);

        $configurator = new EntityManagerConfigurator($dispacher);
        $configurator->configure($entityManager->reveal());

        $ref = new \ReflectionObject($factory);
        $property = $ref->getProperty('dispatcher');
        $property->setAccessible(true);
        $this->assertTrue(null !== $property->getValue($factory));
    }
}
