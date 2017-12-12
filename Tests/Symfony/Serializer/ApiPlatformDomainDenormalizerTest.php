<?php

namespace Biig\Component\Domain\Tests\Symfony\DependencyInjection\Serializer;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Integration\Symfony\Serializer\ApiPlatformDomainDenormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiPlatformDomainDenormalizerTest extends TestCase
{
    /**
     * @var NormalizerInterface
     */
    private $decorated;

    /**
     * @var DomainEventDispatcher
     */
    private $dispatcher;

    public function setUp()
    {
        $this->decorated = $this->prophesize(NormalizerInterface::class);
        $this->decorated->willImplement(DenormalizerInterface::class);
        $this->dispatcher = $this->prophesize(DomainEventDispatcher::class);
    }

    public function testItisAnInstanceOfDenormalize()
    {
        $denormalizer = new ApiPlatformDomainDenormalizer($this->decorated->reveal(), $this->dispatcher->reveal());
        $this->assertInstanceOf(ApiPlatformDomainDenormalizer::class, $denormalizer);
    }

    public function testItSupportsDenormalization()
    {
        $denormalizer =  new ApiPlatformDomainDenormalizer($this->decorated->reveal(), $this->dispatcher->reveal());

        $this->decorated->denormalize([], \stdClass::class, null)->willReturn(false);
        $this->decorated->denormalize([], \FakeModel::class, null)->willReturn(true);

        $this->assertFalse($denormalizer->supportsDenormalization([], \stdClass::class));

        $this->assertTrue($denormalizer->supportsDenormalization([], \FakeModel::class));
    }

    public function testDenormalize()
    {
        $fake = $this->prophesize(\FakeModel::class);

        $this->decorated->denormalize([], \FakeModel::class, null, [])->willReturn($fake)->shouldBeCalled();
        $fake->setDispatcher($this->dispatcher->reveal())->shouldBeCalled();

        $denormalizer =  new ApiPlatformDomainDenormalizer($this->decorated->reveal(), $this->dispatcher->reveal());
        $denormalizer->denormalize([], \FakeModel::class, null, []);
    }
}
