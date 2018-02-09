<?php

namespace Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass;

use Biig\Component\Domain\Integration\Symfony\DependencyInjection\EntityManagerConfigurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InsertDispatcherInClassMetadataFactoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $entityManagers = $container->getParameter('biig_domain.entity_managers');
        if (empty($entityManagers)) {
            $entityManagers = [$container->getParameter('doctrine.default_entity_manager')];
        }

        if ($container->getParameter('biig_domain_doctrine_domain_event_instantiator')) {
            foreach ($entityManagers as $entityManager) {
                $this->addDecoratorToConfigurator($container, $entityManager);
            }
        }
    }

    private function addDecoratorToConfigurator(ContainerBuilder $container, string $entityManager)
    {
        $serviceName = sprintf('biig_domain.%s_configurator', $entityManager);
        $originalConfiguratorName = sprintf('doctrine.orm.%s_manager_configurator', $entityManager);

        $container->register($serviceName, EntityManagerConfigurator::class)
            ->setDecoratedService($originalConfiguratorName)
            ->addArgument(new Reference($serviceName . '.inner'))
            ->addArgument(new Reference('biig_domain.dispatcher'))
            ->setPublic(false)
        ;
    }
}
