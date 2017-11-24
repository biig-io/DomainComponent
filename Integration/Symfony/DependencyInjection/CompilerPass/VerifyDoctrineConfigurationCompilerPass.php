<?php

namespace Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass;

use Biig\Component\Domain\Exception\InvalidConfigurationException;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadataFactory;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class VerifyDoctrineConfigurationCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('biig_domain_doctrine_domain_event_instantiator')) {
            $definitions = $container->getDefinitions();

            foreach ($definitions as $name => $definition) {
                if ($definition instanceof ChildDefinition && 'doctrine.orm.configuration' === $definition->getParent()) {
                    $calls = $definition->getMethodCalls();
                    $this->verifyCalls($calls);
                }
            }
        }
    }

    /**
     * @param array $calls
     *
     * @throws InvalidConfigurationException
     */
    private function verifyCalls(array $calls)
    {
        foreach ($calls as $call) {
            if ('setClassMetadataFactoryName' === $call[0]) {
                if (ClassMetadataFactory::class !== $call[1][0]) {
                    throw new InvalidConfigurationException(
                        'The option "override_doctrine_instantiator", so this bundles tried to change the'
                        . ' ClassMetadataFactory of doctrine by changing the DoctrineBundle configuration.'
                        . ' The final configuration of the doctrine bundle doesn\'t looks like the one expected:'
                        . ' Something probably altered the configuration. You may disable this feature by changing the default'
                        . ' configuration or find what came override this. (It may be your manual configuration)'
                    );
                }
            }
        }
    }
}
