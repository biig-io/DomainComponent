<?php

namespace Biig\Component\Domain\Tests\Symfony\DependencyInjection\CompilerPass;

use Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass\EnableDomainDenormalizerCompilerPass;
use Biig\Component\Domain\Integration\Symfony\Serializer\ApiPlatformDomainDenormalizer;
use Biig\Component\Domain\Integration\Symfony\Serializer\DomainDenormalizer;
use PHPUnit\Framework\TestCase;
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
        $definition = $this->prophesize(Definition::class);
        $container = $this->prophesize(ContainerBuilder::class);
        $container->getParameter('kernel.bundles')->willReturn(['ApiPlatformBundle' => true]);
        $container->register(ApiPlatformDomainDenormalizer::class, ApiPlatformDomainDenormalizer::class)->willReturn($definition)->shouldBeCalled();
        $definition->setDecoratedService('api_platform.jsonld.normalizer.item')->willReturn($definition)->shouldBeCalled();
        $definition->addArgument(new Reference(ApiPlatformDomainDenormalizer::class . '.inner'))->willReturn($definition)->shouldBeCalled();
        $definition->addArgument(new Reference('biig_domain.dispatcher'))->willReturn($definition)->shouldBeCalled();
        $definition->addTag('serializer.normalizer')->willReturn($definition)->shouldBeCalled();
        $definition->setPublic(false)->willReturn($definition)->shouldBeCalled();

        $container->setAlias('biig.domain_normalizer', ApiPlatformDomainDenormalizer::class)->shouldBeCalled();
        $container->register(DomainDenormalizer::class, DomainDenormalizer::class)->shouldNotBeCalled();

        $compilerPass = new EnableDomainDenormalizerCompilerPass();
        $compilerPass->process($container->reveal());
    }

    public function testItAddsDefinitionServiceForDomainDenormalizer()
    {
        $definition = $this->prophesize(Definition::class);
        $container = $this->prophesize(ContainerBuilder::class);
        $container->getParameter('kernel.bundles')->willReturn([]);
        $container->register(ApiPlatformDomainDenormalizer::class, ApiPlatformDomainDenormalizer::class)->willReturn($definition)->shouldNotBeCalled();

        $container->register(DomainDenormalizer::class, DomainDenormalizer::class)->willReturn($definition)->shouldBeCalled();
        $definition->addArgument(new Reference('serializer.normalizer.object'))->willReturn($definition)->shouldBeCalled();
        $definition->addArgument(new Reference('biig_domain.dispatcher'))->willReturn($definition)->shouldBeCalled();
        $definition->addTag('serializer.normalizer')->willReturn($definition)->shouldBeCalled();
        $definition->setPublic(false)->willReturn($definition)->shouldBeCalled();

        $container->setAlias('biig.domain_normalizer', DomainDenormalizer::class)->shouldBeCalled();

        $compilerPass = new EnableDomainDenormalizerCompilerPass();
        $compilerPass->process($container->reveal());
    }
}
