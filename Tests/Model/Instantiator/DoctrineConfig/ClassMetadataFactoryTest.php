<?php

namespace Biig\Component\Domain\Tests\Model\Instantiator\DoctrineConfig;

require_once __DIR__ . '/../../../fixtures/FakeModel.php';

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadata;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadataFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class ClassMetadataFactoryTest extends TestCase
{
    public function testItIsAnInstanceOfDoctrineClassMetadataFactory()
    {
        $factory = new ClassMetadataFactory();
        $this->assertInstanceOf(\Doctrine\ORM\Mapping\ClassMetadataFactory::class, $factory);
    }

    public function testItReturnAnInstanceOfClassMetadata()
    {
        $dbpath = \sys_get_temp_dir() . '/testItReturnAnInstanceOfClassMetadata.' . \microtime() . '.sqlite';

        $config = Setup::createYAMLMetadataConfiguration(array(__DIR__ . '/../../../fixtures/config'), true);
        $config->setClassMetadataFactoryName(ClassMetadataFactory::class);

        $conn = [
            'driver' => 'pdo_sqlite',
            'path' => $dbpath,
        ];
        $entityManager = EntityManager::create($conn, $config);
        $entityManager->getMetadataFactory()->setDispatcher(new DomainEventDispatcher());

        $metadata = $entityManager->getMetadataFactory()->getMetadataFor(\FakeModel::class);

        $this->assertInstanceOf(ClassMetadata::class, $metadata);

        @unlink($dbpath);
    }

    public function testItAllowToRetrieveDomainModel()
    {
        $config = Setup::createYAMLMetadataConfiguration(array(__DIR__ . '/../../../fixtures/config'), true);
        $config->setClassMetadataFactoryName(ClassMetadataFactory::class);

        $dispatcher = $this->prophesize(DomainEventDispatcher::class);
        $dispatcher->dispatch(Argument::cetera())->shouldBeCalled();

        $conn = [
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../../../fixtures/dbtest/fake_model.db',
        ];
        $entityManager = EntityManager::create($conn, $config);
        $entityManager->getMetadataFactory()->setDispatcher($dispatcher->reveal());

        $res = $entityManager->getRepository(\FakeModel::class)->findAll();

        reset($res)->doAction();
    }
}
