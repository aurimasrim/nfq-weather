<?php
/**
 * Created by PhpStorm.
 * User: aurimas
 * Date: 17.11.15
 * Time: 16.19
 */

namespace WeatherBundle\DependencyInjection;

use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
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

        if (array_key_exists('accu', $config['providers'])) {
            $container->register('weather.accu', AccuWeatherProvider::class)
                ->addArgument($config['providers']['accu']['api_key']);

        }

        if (array_key_exists('openweathermap', $config['providers'])) {
            $container->register('weather.openweathermap',OpenWeatherMapWeatherProvider::class)
                ->addArgument($config['providers']['openweathermap']['api_key']);
        }

        if (array_key_exists('cached', $config['providers'])) {
            $container->register('weather.cache', FilesystemAdapter::class)
                ->addArgument('weather')
                ->addArgument($config['providers']['cached']['ttl']);

            $container->register('weather.cached', CachedWeatherProvider::class)
                ->addArgument(new Reference('weather.cache'))
                ->addArgument(new Reference('weather.' . $config['providers']['cached']['provider']))
                ->addArgument(new Reference(LoggerInterface::class));
        }

        if (array_key_exists('delegating', $config['providers'])) {
            $delProviders = [];
            foreach ($config['providers']['delegating']['providers'] as $prov) {
                $delProviders[] = new Reference('weather.' . $prov);
            }

            $container->register('weather.delegating', DelegatingWeatherProvider::class)
                ->addArgument($delProviders)
                ->addArgument(new Reference(LoggerInterface::class));
        }

        $container->setAlias('weather.interface', 'weather.' . $config['provider']);
        $container->setAlias(WeatherProviderInterface::class, 'weather.interface');
    }
}