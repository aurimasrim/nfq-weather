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

        $providers = ['accu', 'openweathermap', 'delegating', 'cached'];

        $rootNode
            ->children()
                ->enumNode('provider')
                    ->values($providers)
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
                                        ->enumPrototype()
                                            ->values($providers)
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('cached')
                                ->children()
                                    ->enumNode('provider')
                                        ->values($providers)
                                    ->end()
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