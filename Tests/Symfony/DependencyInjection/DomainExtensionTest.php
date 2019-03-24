<?php

namespace Biig\Component\Domain\Tests\Symfony\DependencyInjection;

use Biig\Component\Domain\Integration\Symfony\DependencyInjection\DomainExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class DomainExtensionTest extends TestCase
{
    public function testItAddsDoctrinePostPersistListenerToContainer()
    {
        $extension = new DomainExtension();

        $config = [[
            'persist_listeners' => [
                'doctrine' => ['default', 'custom_doctrine'],
            ],
        ]];

        $container = new ContainerBuilder(new ParameterBag([
            'kernel.environment' => 'prod'
        ]));
        $extension->load($config, $container);

        $array = [
            "biig_domain.post_persist_listener.doctrine_default" => [
                [
                    "connection" => "default"
                ]
            ],
            "biig_domain.post_persist_listener.doctrine_custom_doctrine" => [
                [
                    "connection" => "custom_doctrine"
                ]
            ]
        ];

        $this->assertTrue($container->hasDefinition('biig_domain.post_persist_listener.doctrine_default'));
        $this->assertTrue($container->hasDefinition('biig_domain.post_persist_listener.doctrine_custom_doctrine'));

        $this->assertEquals($container->findTaggedServiceIds('doctrine.event_subscriber'), $array);
    }

    public function testItDoesntRegisterDoctrinePostPersistListenerToContainer()
    {
        $extension = new DomainExtension();

        $config = [[]];

        $container = new ContainerBuilder(new ParameterBag([
            'kernel.environment' => 'prod'
        ]));
        $extension->load($config, $container);

        $this->assertFalse($container->hasDefinition('biig_domain.post_persist_listener.doctrine_default'));
    }

    public function testItSetEntityManagersConfigAsParameterOfContainer()
    {
        $extension = new DomainExtension();

        $config = [[
            'entity_managers' => [
                'default',
                'customManager',
            ],
        ]];

        $container = new ContainerBuilder(new ParameterBag([
            'kernel.environment' => 'prod'
        ]));
        $extension->load($config, $container);

        $this->assertTrue($container->hasParameter('biig_domain.entity_managers'));
        $this->assertEquals($container->getParameter('biig_domain.entity_managers'), ['default', 'customManager']);
    }

    public function testItRegisterEventDispatcherTracerInDev()
    {
        $extension = new DomainExtension();

        $container = new ContainerBuilder(new ParameterBag([
            'kernel.environment' => 'dev'
        ]));
        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition('Biig\Component\Domain\Event\DomainEventDispatcherTracer'));
        $this->assertTrue($container->hasDefinition('Biig\Component\Domain\Integration\Symfony\Twig\Profiler\EventsDataCollector'));
    }
}
