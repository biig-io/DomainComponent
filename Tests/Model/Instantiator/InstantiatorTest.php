<?php

namespace Biig\Component\Domain\Tests\Model\Instantiator;

require_once __DIR__ . '/../../fixtures/FakeModel.php';

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\Instantiator\DomainModelInstantiatorInterface;
use Biig\Component\Domain\Model\Instantiator\Instantiator;
use PHPUnit\Framework\TestCase;

class InstantiatorTest extends TestCase
{
    /**
     * @var Instantiator
     */
    private $instantiator;

    public function setUp()
    {
        $dispatcher = new DomainEventDispatcher();
        $this->instantiator = new Instantiator($dispatcher);
    }

    public function testItInstantiateAModelWithDispatcher()
    {
        $model = $this->instantiator->instantiate(\FakeModel::class);

        $this->assertInstanceOf(\FakeModel::class, $model);
        $this->assertTrue($model->hasDispatcher());
    }

    public function testItInstantiateModelWithoutDispatcher()
    {
        $model = $this->instantiator->instantiate(FakeSimpleModel::class);

        $this->assertInstanceOf(FakeSimpleModel::class, $model);
    }

    public function testItInstantiateWithArguments()
    {
        $model = $this->instantiator->instantiateWithArguments(DummyObjectWithConstructor::class, 'hello');

        $this->assertInstanceOf(DummyObjectWithConstructor::class, $model);
        $this->assertEquals('hello', $model->getFoo());
    }

    public function testItShouldBeInstanceOfDomainModelInstantiatorInterface()
    {
        $this->assertInstanceOf(DomainModelInstantiatorInterface::class, $this->instantiator);
    }
}

class FakeSimpleModel
{
}

class DummyObjectWithConstructor
{
    private $foo;

    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }
}
