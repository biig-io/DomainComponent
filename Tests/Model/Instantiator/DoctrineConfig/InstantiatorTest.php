<?php

namespace Biig\Component\Domain\Tests\Model\Instantiator\DoctrineConfig;

require_once (__DIR__ . '/../../../fixtures/FakeModel.php');

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\Instantiator;
use Doctrine\Instantiator\InstantiatorInterface;
use PHPUnit\Framework\TestCase;

class InstantiatorTest extends TestCase
{
    public function testItIsInstanceOfInstantiatorOfDoctrine()
    {
        $doctrineInstantiator = $this->prophesize(InstantiatorInterface::class);
        $instantiator = new Instantiator(new DomainEventDispatcher(), $doctrineInstantiator->reveal());
        $this->assertInstanceOf(InstantiatorInterface::class, $instantiator);
    }

    public function testItUseTheGivenInstantiator()
    {
        $doctrineInstantiator = $this->prophesize(InstantiatorInterface::class);
        $doctrineInstantiator->instantiate(\FakeModel::class)->willReturn(new \FakeModel)->shouldBeCalled();
        $instantiator = new Instantiator(new DomainEventDispatcher(), $doctrineInstantiator->reveal());

        $model = $instantiator->instantiate(\FakeModel::class);
        $this->assertTrue($model->hasDispatcher());
    }
}


