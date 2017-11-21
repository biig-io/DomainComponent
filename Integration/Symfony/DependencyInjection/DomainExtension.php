<?php

namespace Biig\Component\Domain\Integration\Symfony\DependencyInjection;

use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadataFactory;
use Biig\Component\Domain\Rule\DomainRuleInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('biig_domain_doctrine_domain_event_instantiator', $config['override_doctrine_instantiator']);

        $container->registerForAutoconfiguration(DomainRuleInterface::class)->addTag(DomainExtension::DOMAIN_RULE_TAG);
    }

    /**
     * This may fail if a bundle (registered after this one) or a compiler pass modify the parameter.
     * The `VerifyDoctrineConfigurationCompilerPass` verify configuration integrity.
     *
     * @param ContainerBuilder $container
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

    private function buildClassMetadataFactoryConfig($doctrineConfig)
    {
        $doctrineClassMetadataFactoryConfig = [
            'orm' => [
                'entity_managers' => [
                    'default' => [
                        'class_metadata_factory_name' => ClassMetadataFactory::class
                    ]
                ]
            ]
        ];

        if (isset($doctrineConfig[0]['orm']['entity_managers'])) {
            foreach($doctrineConfig[0]['orm']['entity_managers'] as $entityManagerName => $entityManagerConf) {
                $doctrineClassMetadataFactoryConfig['orm']['entity_managers'][$entityManagerName]['class_metadata_factory_name'] = ClassMetadataFactory::class;
            }
        }

        return $doctrineClassMetadataFactoryConfig;
    }
}
