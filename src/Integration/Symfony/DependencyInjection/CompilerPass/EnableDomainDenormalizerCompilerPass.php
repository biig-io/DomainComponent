<?php

namespace Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass;

use Biig\Component\Domain\Integration\Symfony\Serializer\DomainDenormalizer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EnableDomainDenormalizerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $serviceId = 'biig.domain_denormalizer';

        if ($container->hasDefinition('api_platform.jsonld.normalizer.item')) {
            $container->register($serviceId . '.api_platform.jsonld', DomainDenormalizer::class)
                ->setDecoratedService('api_platform.jsonld.normalizer.item')
                ->addArgument(new Reference($serviceId . '.api_platform.jsonld.inner'))
                ->addArgument(new Reference('biig_domain.dispatcher'))
                ->setPublic(false);
        }

        if ($container->hasDefinition('api_platform.serializer.normalizer.item')) {
            $container->register($serviceId . '.api_platform.json', DomainDenormalizer::class)
                ->setDecoratedService('api_platform.serializer.normalizer.item')
                ->addArgument(new Reference($serviceId . '.api_platform.json.inner'))
                ->addArgument(new Reference('biig_domain.dispatcher'))
                ->setPublic(false)
            ;
        }

        if ($container->hasDefinition('api_platform.hal.normalizer.item')) {
            $container->register($serviceId . '.api_platform.hal', DomainDenormalizer::class)
                ->setDecoratedService('api_platform.hal.normalizer.item')
                ->addArgument(new Reference($serviceId . '.api_platform.hal.inner'))
                ->addArgument(new Reference('biig_domain.dispatcher'))
                ->setPublic(false)
            ;
        }

        if ($container->hasDefinition('serializer.normalizer.object')) {
            $container->register($serviceId, DomainDenormalizer::class)
                ->setDecoratedService('serializer.normalizer.object')
                ->addArgument(new Reference($serviceId . '.inner'))
                ->addArgument(new Reference('biig_domain.dispatcher'))
                ->setPublic(false)
            ;
        }
    }
}
