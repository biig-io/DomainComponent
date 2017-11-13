<?php

namespace Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass;


use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\EntityManagerConfigurator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InsertDispatcherInClassMetadataFactoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('biig_domain_doctrine_domain_event_instantiator')) {
            $definitions = $container->getDefinitions();

            foreach ($definitions as $name => $definition) {
                if (
                    $definition instanceof ChildDefinition
                    && 'doctrine.orm.entity_manager.abstract' === $definition->getParent()
                ) {
                    $definition->setConfigurator([new Reference(EntityManagerConfigurator::class), 'configure']);
                }
            }
        }
    }
}
