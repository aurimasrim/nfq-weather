<?php
/**
 * Created by PhpStorm.
 * User: aurimas
 * Date: 17.10.17
 * Time: 15.30
 */
namespace WeatherBundle\Utils;

use Psr\Log\LoggerInterface;
use WeatherBundle\Exception\WeatherProviderException;
use WeatherBundle\Model\Location;
use WeatherBundle\Model\Weather;

class DelegatingWeatherProvider implements WeatherProviderInterface
{
    /**
     * @var WeatherProviderInterface[]
     */
    private $providers;
    private $logger;

    /**
     * @param WeatherProviderInterface[] $providers
     * @param LoggerInterface $logger
     * @throws WeatherProviderException
     */
    public function __construct(array $providers, LoggerInterface $logger)
    {
        $this->logger = $logger;

        foreach ($providers as $provider)
        {

            if (is_a($provider, 'WeatherBundle\\WeatherProviderInterface'))
            {
                $this->providers[] = $provider;
            }
        }

        if (empty($this->providers))
        {
            throw new WeatherProviderException('Invalid constructor parameters.');
        }
    }

    public function fetch(Location $location, int $units = 0): Weather
    {
        foreach($this->providers as $provider)
        {
            try {
                return $provider->fetch($location, $units);

            } catch(WeatherProviderException $ex){
                $this->logger->error($ex);
            }
        }
        throw new WeatherProviderException('No accessible providers.');
    }
}