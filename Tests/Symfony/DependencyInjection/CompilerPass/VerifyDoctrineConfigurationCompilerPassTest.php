<?php

namespace Biig\Component\Domain\Tests\Symfony\DependencyInjection\CompilerPass;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass\VerifyDoctrineConfigurationCompilerPass;
use Doctrine\ORM\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class VerifyDoctrineConfigurationCompilerPassTest extends TestCase
{
    public function testItIsASymfonyCompilerPass()
    {
        $pass = new VerifyDoctrineConfigurationCompilerPass();
        $this->assertInstanceOf(CompilerPassInterface::class, $pass);
    }

    /**
     * @expectedException \Biig\Component\Domain\Exception\InvalidConfigurationException
     */
    public function testItThrowsAnErrorWhenTheConfigurationIsNotModifiedAsExpected()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->getParameter('biig_domain_doctrine_domain_event_instantiator')->willReturn(true);
        $config = new Definition(Configuration::class);
        $configChild = new ChildDefinition('doctrine.orm.configuration');

        // The method call is to our ClassMetadataFactory if the previous parameter is defined,
        // if something alter the value, we need to throw an error.
        $configChild->addMethodCall('setClassMetadataFactoryName', [null]);

        $container->getDefinitions()->willReturn([
            'doctrine.orm.configuration' => $config,
            'doctrine.orm.configuration.whatever' => $configChild,
        ]);

        $compilerPass = new VerifyDoctrineConfigurationCompilerPass();
        $compilerPass->process($container->reveal());
    }

    public function testItDoesNothingWhenFeatureNotActivated()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->getParameter('biig_domain_doctrine_domain_event_instantiator')->willReturn(false)->shouldBeCalled();
        $container->getDefinitions([])->shouldNotBeCalled();

        $compilerPass = new VerifyDoctrineConfigurationCompilerPass();
        $compilerPass->process($container->reveal());
    }

    public function testItWorksWithFeatureActivatedButNoDoctrineConfiguration()
    {
        $container = new ContainerBuilder();
        $container->setParameter('biig_domain_doctrine_domain_event_instantiator', true);
        $container->setDefinition('dispatcher', new Definition(DomainEventDispatcher::class));

        $compilerPass = new VerifyDoctrineConfigurationCompilerPass();
        $this->assertNull($compilerPass->process($container));
    }
}
