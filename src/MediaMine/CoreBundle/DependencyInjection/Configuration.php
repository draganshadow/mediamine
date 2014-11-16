<?php

namespace MediaMine\CoreBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('media_mine_core');

        $rootNode
            ->children()
                ->arrayNode('system')->children()
                    ->arrayNode('paths')->children()
                        ->scalarNode('ffmpeg')->end()
                        ->scalarNode('ffprobe')->end()
                    ->end()->end()
                ->end()->end()
                ->arrayNode('modules')
                    ->prototype('array')->children()
                        ->arrayNode('module')->children()
                            ->scalarNode('key')->end()
                            ->scalarNode('namespace')->end()
                            ->scalarNode('name')->end()
                            ->scalarNode('version')->end()
                            ->scalarNode('lock')->end()
                            ->scalarNode('installed')->end()
                            ->scalarNode('enabled')->end()
                        ->end()->end()
                        ->arrayNode('tunnels')
                            ->prototype('array')->children()
                                ->scalarNode('key')->end()
                                ->scalarNode('service')->end()
                                ->scalarNode('enabled')->end()
                            ->end()->end()
                        ->end()
                        ->arrayNode('filetypes')
                            ->prototype('array')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                        ->arrayNode('videotypes')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('grouptypes')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('staffroles')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('settings')
                            ->prototype('array')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                        ->arrayNode('actions')
                            ->prototype('array')->children()
                                ->scalarNode('service')->end()
                                ->scalarNode('method')->end()
                            ->end()->end()
                        ->end()
                    ->end()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
