<?php
/**
 * Created by PhpStorm.
 * User: aurimas
 * Date: 17.10.18
 * Time: 17.40
 */

namespace WeatherBundle\Utils;


use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use WeatherBundle\Exception\WeatherProviderException;
use WeatherBundle\Model\Location;
use WeatherBundle\Model\Weather;

class CachedWeatherProvider implements WeatherProviderInterface
{
    private $cache;
    private $weatherProvider;
    private $logger;

    public function __construct(CacheItemPoolInterface $cache, WeatherProviderInterface $weatherProvider, LoggerInterface $logger)
    {
        $this->cache = $cache;
        $this->weatherProvider = $weatherProvider;
        $this->logger = $logger;
    }

    public function fetch(Location $location, int $units = 0): Weather
    {
        $key = 'Weather'
            . $location->getLon() . ','
            . $location->getLat() . ','
            . $units;

        $item = null;
        try {
            $item = $this->cache->getItem($key);
        } catch (InvalidArgumentException $ex) {
            throw new WeatherProviderException('Invalid cache item key.');
        }


        if ($item->isHit())
        {
            return $item->get();
        }
        else
        {
            $weather = $this->weatherProvider->fetch($location, $units);
            $item->set($weather);

            if (!$this->cache->save($item))
            {
                $this->logger->error('Failed to persist cache item. Stack trace: '
                    . (new \Exception)->getTraceAsString());
            }

            return $weather;
        }
    }
}