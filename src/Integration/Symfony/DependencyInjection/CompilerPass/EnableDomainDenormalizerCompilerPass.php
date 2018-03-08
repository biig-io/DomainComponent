<?php

namespace Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass;

use Biig\Component\Domain\Integration\Symfony\Serializer\ApiPlatformDomainDenormalizer;
use Biig\Component\Domain\Integration\Symfony\Serializer\DomainDenormalizer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EnableDomainDenormalizerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        $alias = 'biig.domain_normalizer';

        if (array_key_exists('ApiPlatformBundle', $bundles)) {
            $container->register(ApiPlatformDomainDenormalizer::class, ApiPlatformDomainDenormalizer::class)
                ->setDecoratedService('api_platform.jsonld.normalizer.item')
                ->addArgument(new Reference(ApiPlatformDomainDenormalizer::class . '.inner'))
                ->addArgument(new Reference('biig_domain.dispatcher'))
                ->addTag('serializer.normalizer')
                ->setPublic(false)
            ;
            $container->setAlias($alias, ApiPlatformDomainDenormalizer::class);
        } else {
            $container->register(DomainDenormalizer::class, DomainDenormalizer::class)
                ->addArgument(new Reference('serializer.normalizer.object'))
                ->addArgument(new Reference('biig_domain.dispatcher'))
                ->addTag('serializer.normalizer')
                ->setPublic(false)
            ;
            $container->setAlias($alias, DomainDenormalizer::class);
        }
    }
}
