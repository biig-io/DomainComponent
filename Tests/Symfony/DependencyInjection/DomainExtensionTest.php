<?php

namespace Biig\Component\Domain\Tests\Symfony\DependencyInjection;

use Biig\Component\Domain\Integration\Symfony\DependencyInjection\DomainExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DomainExtensionTest extends TestCase
{
    public function testItAddsDoctrinePostPersistListenerToContainer()
    {
        $extension = new DomainExtension();

        $config = [[
            'persist_listeners' => [
                'doctrine' => ['default'],
            ],
        ]];

        $container = new ContainerBuilder();
        $extension->load($config, $container);

        $this->assertTrue($container->hasDefinition('biig_domain.post_persist_listener.doctrine_default'));
    }

    public function testItDoesntRegisterDoctrinePostPersistListenerToContainer()
    {
        $extension = new DomainExtension();

        $config = [[]];

        $container = new ContainerBuilder();
        $extension->load($config, $container);

        $this->assertFalse($container->hasDefinition('biig_domain.post_persist_listener.doctrine_default'));
    }

    public function testItSetEntityManagersConfigAsParameterOfContainer()
    {
        $extension = new DomainExtension();

        $config = [[
            'entity_managers' => [
                'default',
                'customManager'
            ],
        ]];

        $container = new ContainerBuilder();
        $extension->load($config, $container);

        $this->assertTrue($container->hasParameter('biig_domain.entity_managers'));
        $this->assertEquals($container->getParameter('biig_domain.entity_managers'), ['default', 'customManager']);
    }
}
