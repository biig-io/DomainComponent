<?php

namespace Biig\Component\Domain\Tests\Symfony\DependencyInjection\CompilerPass;

use Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass\InsertDispatcherInClassMetadataFactoryCompilerPass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InsertDispatcherInClassMetadataFactoryCompilerPassTest extends TestCase
{
    public function testItIsASymfonyCompilerPass()
    {
        $pass = new InsertDispatcherInClassMetadataFactoryCompilerPass();
        $this->assertInstanceOf(CompilerPassInterface::class, $pass);
    }

    public function testItAddsConfigurationForEntityManagers()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->getParameter('biig_domain_doctrine_domain_event_instantiator')->willReturn(true);
        $configChild = $this->prophesize(ChildDefinition::class);
        $configChild->getParent()->willReturn('doctrine.orm.entity_manager.abstract');
        $container->getDefinitions()->willReturn([
            'doctrine.orm.default_entity_manager' => $configChild->reveal()
        ]);

        $configChild->setConfigurator(Argument::any())->shouldBeCalled();

        $compilerPass = new InsertDispatcherInClassMetadataFactoryCompilerPass();
        $compilerPass->process($container->reveal());
    }

    public function testItDoesNotAddConfigurationForEntityManagers()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->getParameter('biig_domain_doctrine_domain_event_instantiator')->willReturn(false);
        $configChild = $this->prophesize(ChildDefinition::class);
        $configChild->getParent()->willReturn('doctrine.orm.entity_manager.abstract');
        $container->getDefinitions()->willReturn([
            'doctrine.orm.default_entity_manager' => $configChild->reveal()
        ]);

        $configChild->setConfigurator(Argument::any())->shouldNotBeCalled();

        $compilerPass = new InsertDispatcherInClassMetadataFactoryCompilerPass();
        $compilerPass->process($container->reveal());
    }
}
