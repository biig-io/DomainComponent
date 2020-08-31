<?php

namespace Biig\Component\Domain\Tests\Model\Instantiator\DoctrineConfig;

require_once __DIR__ . '/../../../fixtures/FakeModel.php';

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadata;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\Instantiator;
use PHPUnit\Framework\TestCase;

class ClassMetadataTest extends TestCase
{
    /**
     * @var ClassMetadata
     */
    private $metadata;

    public function setUp()
    {
        $this->metadata = new ClassMetadata(\FakeModel::class, new Instantiator(new DomainEventDispatcher()));
    }

    public function testItIsInstanceOfDoctrineClassMetadata()
    {
        $this->assertInstanceOf(\Doctrine\ORM\Mapping\ClassMetadata::class, $this->metadata);
    }

    public function testItInstantiateEntities()
    {
        $model = $this->metadata->newInstance();

        $this->assertInstanceOf(\FakeModel::class, $model);
    }

    public function testItsWakable()
    {
        $metadata = unserialize(serialize($this->metadata));

        $this->assertInstanceOf(ClassMetadata::class, $metadata);

        if (interface_exists(\Doctrine\Persistence\Mapping\ReflectionService::class)) {
            $refSer = $this->prophesize(\Doctrine\Persistence\Mapping\ReflectionService::class);
        } else {
            $refSer = $this->prophesize(\Doctrine\Common\Persistence\Mapping\ReflectionService::class);
        }
        $metadata->wakeupReflectionWithInstantiator($refSer->reveal(), new Instantiator(new DomainEventDispatcher()));

        $model = $metadata->newInstance();

        $this->assertInstanceOf(\FakeModel::class, $model);
        $this->assertTrue($model->hasDispatcher());
    }
}
