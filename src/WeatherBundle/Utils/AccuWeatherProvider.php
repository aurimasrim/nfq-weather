<?php
/**
 * Created by PhpStorm.
 * User: aurimas
 * Date: 17.10.17
 * Time: 15.27
 */

namespace WeatherBundle\Utils;


use WeatherBundle\Exception\WeatherProviderException;
use WeatherBundle\Model\Location;
use WeatherBundle\Model\Weather;

class AccuWeatherProvider implements  WeatherProviderInterface
{
    private const URLSTART = 'http://dataservice.accuweather.com';
    private const LOCATIONURI = '/locations/v1/cities/geoposition/search.json?';
    private const WEATHERURI = '/currentconditions/v1/';

    private $zapiKey;

    public function __construct($apiKey)
    {
        $this->units = $this::UNITS_METRIC;
        $this->apiKey = $apiKey;
    }

    public function fetch(Location $location, int $units = 0): Weather
    {
        if ($units !== 0 && $units !== 1)
        {
            throw new WeatherProviderException("Invalid units argument");
        }

        $locationKey = $this->fetchLocationKey($location);

        $url = $this::URLSTART
            . $this::WEATHERURI
            . $locationKey . '?'
            . http_build_query(['apikey' => $this->apiKey, 'details' => 'true']);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if ($result === false)
        {
            throw new WeatherProviderException('Weather provider is not accessible.');
        }
        return $this->parseWeather($result, $units);
    }

    private function fetchLocationKey(Location $location): string
    {
        $array = [
            'q' => $location->getLon() . ',' . $location->getLat(),
            'apikey' => $this->apiKey
        ];

        $url = $this::URLSTART
            . $this::LOCATIONURI
            . http_build_query($array);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if ($result === false)
        {
            throw new WeatherProviderException('Location provider is not accessible.');
        }
        return $this->parseLocationKey($result);
    }

    private function parseLocationKey($value): string
    {
        $json = json_decode($value, true);
        if ($json === null)
        {
            throw new WeatherProviderException('Invalid provider location response. Bad file format.');
        }

        if (!array_key_exists('Key', $json) || empty($json['Key']))
        {
            throw new WeatherProviderException('Invalid provider location response. JSON file do not hold required information.');
        }
        return $json['Key'];
    }

    private function parseWeather($value, $units): Weather
    {
        $json = json_decode($value, true);
        if ($json === null)
        {
            throw new WeatherProviderException('Invalid provider weather response. Bad file format.');
        }

        $strUnits = '';
        if ($units === $this::UNITS_METRIC)
        {
            $strUnits = 'Metric';
        }
        else
        {
            $strUnits = 'Imperial';
        }

        $temperature = $json['0']['Temperature'][$strUnits]['Value'];
        if (empty($temperature))
        {
            throw new WeatherProviderException('Invalid provider weather response. JSON file do not hold required information.');
        }
        $pressure = $json['0']['Pressure'][$strUnits]['Value'];
        $humidity = $json['0']['RelativeHumidity'];
        $windSpeed = $json['0']['Wind']['Speed'][$strUnits]['Value'];
        return new Weather($temperature, $pressure, $humidity, $windSpeed);
    }
}