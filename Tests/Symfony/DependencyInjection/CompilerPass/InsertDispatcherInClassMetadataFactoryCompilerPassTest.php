<?php

namespace Biig\Component\Domain\Tests\Symfony\DependencyInjection\CompilerPass;

use Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass\InsertDispatcherInClassMetadataFactoryCompilerPass;
use Biig\Component\Domain\Integration\Symfony\DependencyInjection\EntityManagerConfigurator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class InsertDispatcherInClassMetadataFactoryCompilerPassTest extends TestCase
{
    public function testItIsASymfonyCompilerPass()
    {
        $pass = new InsertDispatcherInClassMetadataFactoryCompilerPass();
        $this->assertInstanceOf(CompilerPassInterface::class, $pass);
    }

    public function testItDecorateEntityManagersConfigurators()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->getParameter('biig_domain.entity_managers')->willReturn(['hello', 'world']);
        $container->getParameter('biig_domain_doctrine_domain_event_instantiator')->willReturn(true);

        $defHello = new Definition();
        $defWorld = new Definition();

        $container->register('biig_domain.hello_configurator', EntityManagerConfigurator::class)->shouldBeCalled()->willReturn($defHello);
        $container->register('biig_domain.world_configurator', EntityManagerConfigurator::class)->shouldBeCalled()->willReturn($defWorld);

        $compilerPass = new InsertDispatcherInClassMetadataFactoryCompilerPass();
        $compilerPass->process($container->reveal());

        $this->assertFalse($defHello->isPublic());
        $this->assertFalse($defWorld->isPublic());

        $this->assertInstanceOf(Reference::class, $defHello->getArgument(0));
        $this->assertInstanceOf(Reference::class, $defWorld->getArgument(0));

        $this->assertEquals((string) $defHello->getArgument(0), 'biig_domain.hello_configurator.inner');
        $this->assertEquals((string) $defWorld->getArgument(0), 'biig_domain.world_configurator.inner');
        $this->assertEquals((string) $defHello->getArgument(1), 'biig_domain.dispatcher');
        $this->assertEquals((string) $defWorld->getArgument(1), 'biig_domain.dispatcher');
    }

    public function testItUseDoctrineDefaultIfNoEntityManagerProvided()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->getParameter('biig_domain.entity_managers')->willReturn([]);
        $container->getParameter('doctrine.default_entity_manager')->willReturn('default');
        $container->getParameter('biig_domain_doctrine_domain_event_instantiator')->willReturn(true);

        $configurator = new Definition();

        $container->register('biig_domain.default_configurator', EntityManagerConfigurator::class)->shouldBeCalled()->willReturn($configurator);

        $compilerPass = new InsertDispatcherInClassMetadataFactoryCompilerPass();
        $compilerPass->process($container->reveal());

        $this->assertFalse($configurator->isPublic());
        $this->assertInstanceOf(Reference::class, $configurator->getArgument(0));
        $this->assertEquals((string) $configurator->getArgument(0), 'biig_domain.default_configurator.inner');
        $this->assertEquals((string) $configurator->getArgument(1), 'biig_domain.dispatcher');
    }

    public function testItDoesNotAddConfigurationForEntityManagers()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->getParameter('biig_domain.entity_managers')->willReturn(['hello', 'world']);
        $container->getParameter('biig_domain_doctrine_domain_event_instantiator')->willReturn(false);

        $container->register(Argument::cetera())->shouldNotBeCalled();

        $compilerPass = new InsertDispatcherInClassMetadataFactoryCompilerPass();
        $compilerPass->process($container->reveal());
    }
}
