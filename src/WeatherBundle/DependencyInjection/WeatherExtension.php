<?php
/**
 * Created by PhpStorm.
 * User: aurimas
 * Date: 17.11.15
 * Time: 16.19
 */

namespace WeatherBundle\DependencyInjection;

use Monolog\Logger;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use WeatherBundle\Utils\AccuWeatherProvider;
use WeatherBundle\Utils\CachedWeatherProvider;
use WeatherBundle\Utils\DelegatingWeatherProvider;
use WeatherBundle\Utils\OpenWeatherMapWeatherProvider;
use WeatherBundle\Utils\WeatherProviderInterface;

class WeatherExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

//        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
//        $loader->load('accu.yml');


        if ($config['providers']['accu']) {
            $container->register('weather.accu', AccuWeatherProvider::class)
                ->addArgument($config['providers']['accu']['api_key']);

        }

        if ($config['providers']['openweathermap']) {
            $container->register('weather.openweathermap',OpenWeatherMapWeatherProvider::class)
                ->addArgument($config['providers']['openweathermap']['api_key']);
        }

        if ($config['providers']['cached']) {
            $container->register('weather.cached', CachedWeatherProvider::class)
                ->addArgument(new Reference(CacheItemPoolInterface::class))
                ->addArgument(new Reference('weather.' . $config['providers']['cached']['provider']))
                ->addArgument(new Reference(LoggerInterface::class));
        }
        $delProviders = [];


        foreach ($config['providers']['delegating']['providers'] as $prov) {
            $delProviders[] = new Reference('weather.' . $prov);
        }

        if ($config['providers']['delegating']) {
            $container->register('weather.delegating', DelegatingWeatherProvider::class)
                ->addArgument($delProviders)
                ->addArgument(new Reference('monolog.logger'));
        }

        $container->setAlias('weather.interface', 'weather.' . $config['provider']);
        $container->setAlias(WeatherProviderInterface::class, 'weather.interface');
    }
}