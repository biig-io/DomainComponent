<?php

namespace Biig\Component\Domain\Tests\Symfony\DependencyInjection\Serializer;

require_once (__DIR__ . '/../../fixtures/FakeModel.php');

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Integration\Symfony\Serializer\DomainDenormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class DomainDenormalizerTest extends TestCase
{
    /**
     * @var ObjectNormalizer
     */
    private $decorated;

    /**
     * @var DomainEventDispatcher
     */
    private $dispatcher;

    public function setUp()
    {
        $this->decorated = $this->prophesize(ObjectNormalizer::class);
        $this->dispatcher = $this->prophesize(DomainEventDispatcher::class);
    }

    public function testItIsAnInstanceOfDenormalize()
    {
        $denormalizer = new DomainDenormalizer($this->decorated->reveal(), $this->dispatcher->reveal());
        $this->assertInstanceOf(DenormalizerInterface::class, $denormalizer);
    }

    public function testItSupportsDenormalization()
    {
        $denormalizer = new DomainDenormalizer($this->decorated->reveal(), $this->dispatcher->reveal());

        $this->assertFalse($denormalizer->supportsDenormalization([], \stdClass::class, []));

        $this->assertTrue($denormalizer->supportsDenormalization([], \FakeModel::class, []));
    }

    public function testDenormalize()
    {
        $fake = $this->prophesize(\FakeModel::class);

        $this->decorated->denormalize([], \FakeModel::class, null, [])->willReturn($fake)->shouldBeCalled();
        $fake->setDispatcher($this->dispatcher->reveal())->shouldBeCalled();

        $denormalizer = new DomainDenormalizer($this->decorated->reveal(), $this->dispatcher->reveal());
        $denormalizer->denormalize([], \FakeModel::class, null, []);
    }
}
