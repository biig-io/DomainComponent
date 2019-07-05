<?php

namespace Biig\Component\Domain\Integration\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('biig_domain');

        $treeBuilder
            ->getRootNode()
            ->children()
                ->booleanNode('override_doctrine_instantiator')
                    ->defaultTrue()
                ->end()
                ->arrayNode('entity_managers')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('persist_listeners')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('doctrine')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
