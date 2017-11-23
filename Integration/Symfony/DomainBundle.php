<?php

namespace Biig\Component\Domain\Integration\Symfony;

use Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass\InsertDispatcherInClassMetadataFactoryCompilerPass;
use Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass\RegisterDomainRulesCompilerPass;
use Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass\VerifyDoctrineConfigurationCompilerPass;
use Biig\Component\Domain\Integration\Symfony\DependencyInjection\DomainExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DomainBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new DomainExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new VerifyDoctrineConfigurationCompilerPass());
        $container->addCompilerPass(new InsertDispatcherInClassMetadataFactoryCompilerPass());
        $container->addCompilerPass(new RegisterDomainRulesCompilerPass());
    }
}
