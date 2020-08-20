<?php

namespace Biig\Component\Domain\Integration\Symfony\DependencyInjection;

use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadataFactory;
use Biig\Component\Domain\PostPersistListener\DoctrinePostPersistListener;
use Biig\Component\Domain\Rule\RuleInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class DomainExtension extends Extension implements PrependExtensionInterface
{
    const DOMAIN_RULE_TAG = 'biig_domain.rule';

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        if (class_exists('Symfony\\Bundle\\WebProfilerBundle\\DependencyInjection\\WebProfilerExtension') && $container->getParameter('kernel.debug')) {
            $loader->load('services.debug.yaml');
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('biig_domain_doctrine_domain_event_instantiator', $config['override_doctrine_instantiator']);

        $container->registerForAutoconfiguration(RuleInterface::class)->addTag(self::DOMAIN_RULE_TAG);

        $container->setParameter('biig_domain.entity_managers', $config['entity_managers']);

        if (!empty($config['persist_listeners']['doctrine'])) {
            $this->registerDoctrinePostPersistListener($config['persist_listeners']['doctrine'], $container);
        }
    }

    /**
     * This may fail if a bundle (registered after this one) or a compiler pass modify the parameter.
     * The `VerifyDoctrineConfigurationCompilerPass` verify configuration integrity.
     */
    public function prepend(ContainerBuilder $container)
    {
        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            // Pre-process the configuration
            $configs = $container->getExtensionConfig($this->getAlias());
            $config = $this->processConfiguration(new Configuration(), $configs);

            // This is true by default
            if ($config['override_doctrine_instantiator']) {
                $doctrineConfig = $container->getExtensionConfig('doctrine');
                $doctrineClassMetadataFactoryConfig = $this->buildClassMetadataFactoryConfig($doctrineConfig);

                $container->prependExtensionConfig('doctrine', $doctrineClassMetadataFactoryConfig);
            }
        }
    }

    public function getAlias()
    {
        return 'biig_domain';
    }

    private function registerDoctrinePostPersistListener(array $config, ContainerBuilder $container)
    {
        foreach ($config as $connection) {
            $container
                ->autowire(
                    sprintf('biig_domain.post_persist_listener.doctrine_%s', $connection),
                    DoctrinePostPersistListener::class
                )
                ->setArgument(0, new Reference('biig_domain.dispatcher'))
                ->addTag('doctrine.event_subscriber', ['connection' => $connection])
            ;
        }
    }

    private function buildClassMetadataFactoryConfig(array $doctrineConfig)
    {
        $doctrineClassMetadataFactoryConfig = [
            'orm' => [
                'entity_managers' => [
                    'default' => [
                        'class_metadata_factory_name' => ClassMetadataFactory::class,
                    ],
                ],
            ],
        ];

        if (isset($doctrineConfig[0]['orm']['entity_managers'])) {
            foreach ($doctrineConfig[0]['orm']['entity_managers'] as $entityManagerName => $entityManagerConf) {
                $doctrineClassMetadataFactoryConfig['orm']['entity_managers'][$entityManagerName]['class_metadata_factory_name'] = ClassMetadataFactory::class;
            }
        }

        return $doctrineClassMetadataFactoryConfig;
    }
}
