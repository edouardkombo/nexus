<?php

namespace NexusBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nexus');

        $rootNode
            ->children()
                ->scalarNode('google_translate_api_key')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}