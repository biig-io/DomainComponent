<?php

namespace Biig\Component\Domain\Tests\Symfony\DependencyInjection\CompilerPass;

use Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass\EnableDomainDenormalizerCompilerPass;
use Biig\Component\Domain\Integration\Symfony\Serializer\DomainDenormalizer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class EnableDomainDenormalizerCompilerPassTest extends TestCase
{
    public function testItIsASymfonyCompilerPass()
    {
        $pass = new EnableDomainDenormalizerCompilerPass();
        $this->assertInstanceOf(CompilerPassInterface::class, $pass);
    }

    public function testItAddsDefinitionServiceForApiPlatformDomainDenormalizer()
    {
        $compilerPass = new EnableDomainDenormalizerCompilerPass();
        $compilerPass->process($this->getContainerMock(true));
    }

    public function testItAddsDefinitionServiceForDomainDenormalizer()
    {
        $compilerPass = new EnableDomainDenormalizerCompilerPass();
        $compilerPass->process($this->getContainerMock(false, true));
    }

    private function getContainerMock($apiPlatform = false, $symfonySerializer = false)
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $definition = $this->prophesize(Definition::class);

        $container->hasDefinition('api_platform.jsonld.normalizer.item')->willReturn($apiPlatform ? $apiPlatform : []);
        $container->hasDefinition('api_platform.serializer.normalizer.item')->willReturn($apiPlatform ? $apiPlatform : []);
        $container->hasDefinition('api_platform.hal.normalizer.item')->willReturn($apiPlatform ? $apiPlatform : []);
        $container->hasDefinition('serializer.normalizer.object')->willReturn($symfonySerializer ? $symfonySerializer : []);

        if ($apiPlatform) {
            //api_platform.jsonld.normalizer.item
            $definition->setDecoratedService(Argument::type('string'))->willReturn($definition)->shouldBeCalled();
            $definition->addArgument(Argument::type(Reference::class))->willReturn($definition)->shouldBeCalled();
            $definition->addArgument(new Reference('biig_domain.dispatcher'))->willReturn($definition)->shouldBeCalled();
            $definition->setPublic(false)->willReturn($definition)->shouldBeCalled();
            $container->register('biig.domain_denormalizer.api_platform.jsonld', DomainDenormalizer::class)->willReturn($definition);

            //api_platform.serializer.normalizer.item
            $definition->setDecoratedService(Argument::type('string'))->willReturn($definition)->shouldBeCalled();
            $definition->addArgument(Argument::type(Reference::class))->willReturn($definition)->shouldBeCalled();
            $definition->addArgument(new Reference('biig_domain.dispatcher'))->willReturn($definition)->shouldBeCalled();
            $definition->setPublic(false)->willReturn($definition)->shouldBeCalled();
            $container->register('biig.domain_denormalizer.api_platform.json', DomainDenormalizer::class)->willReturn($definition);

            //api_platform.hal.normalizer.item
            $definition->setDecoratedService(Argument::type('string'))->willReturn($definition)->shouldBeCalled();
            $definition->addArgument(Argument::type(Reference::class))->willReturn($definition)->shouldBeCalled();
            $definition->addArgument(new Reference('biig_domain.dispatcher'))->willReturn($definition)->shouldBeCalled();
            $definition->setPublic(false)->willReturn($definition)->shouldBeCalled();
            $container->register('biig.domain_denormalizer.api_platform.hal', DomainDenormalizer::class)->willReturn($definition);

        }

        if ($symfonySerializer) {
            $definition->setDecoratedService(Argument::type('string'))->willReturn($definition)->shouldBeCalled();
            $definition->addArgument(Argument::type(Reference::class))->willReturn($definition)->shouldBeCalled();
            $definition->addArgument(new Reference('biig_domain.dispatcher'))->willReturn($definition)->shouldBeCalled();
            $definition->setPublic(false)->willReturn($definition)->shouldBeCalled();
            $container->register('biig.domain_denormalizer', DomainDenormalizer::class)->willReturn($definition);
        }

        return $container->reveal();
    }
}
