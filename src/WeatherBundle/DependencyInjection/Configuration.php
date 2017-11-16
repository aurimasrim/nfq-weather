<?php

namespace WeatherBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nfq_weather');

        $rootNode
            ->children()
                ->enumNode('provider')
                    ->values(['accu', 'openweathermap', 'delegating', 'cached'])
                ->end()
                ->arrayNode('providers')
                    ->isRequired()
                        ->children()
                            ->arrayNode('accu')
                                ->children()
                                    ->scalarNode('api_key')->isRequired()->end()
                                ->end()
                            ->end()
                            ->arrayNode('openweathermap')
                                ->children()
                                    ->scalarNode('api_key')->isRequired()->end()
                                ->end()
                            ->end()
                            ->arrayNode('delegating')
                                ->children()
                                    ->arrayNode('providers')
                                        ->scalarPrototype()->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('cached')
                                ->children()
                                    ->scalarNode('provider')->end()
                                    ->integerNode('ttl')->end()
                                ->end()
                            ->end()
                        ->end()
                ->end()
            ->end()
            ;
        return $treeBuilder;
    }
}