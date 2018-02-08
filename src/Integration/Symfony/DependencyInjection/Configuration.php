<?php

namespace Biig\Component\Domain\Integration\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('biig_domain');

        $rootNode
            ->children()
                ->booleanNode('override_doctrine_instantiator')
                    ->defaultTrue()
                ->end()
                ->arrayNode('entity_managers')
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
