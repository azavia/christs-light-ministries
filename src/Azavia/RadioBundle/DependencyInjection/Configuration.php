<?php

namespace Azavia\RadioBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('azavia_radio');
        $rootNode
        ->children()
        ->scalarNode('processed_track_dir')->defaultValue('%kernel.root_dir%/uploads/tracks/processed')->end()
        ->scalarNode('unprocessed_track_dir')->defaultValue('%kernel.root_dir%/uploads/tracks/unprocessed')->end()
        ->end();

        return $treeBuilder;
    }
}
