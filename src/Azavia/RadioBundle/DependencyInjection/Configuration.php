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
        ->scalarNode('live365_username')->defaultValue('christs_light')->end()
        ->scalarNode('live365_password')->defaultValue('piano2188')->end()
        ->arrayNode('twitter')
        ->children()
        ->scalarNode('consumer_key')->end()
        ->scalarNode('consumer_secret')->end()
        ->scalarNode('access_token')->end()
        ->scalarNode('access_token_secret')->end()
->end()
        ->end();

        return $treeBuilder;
    }
}
