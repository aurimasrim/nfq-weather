<?php
/**
 * Created by PhpStorm.
 * User: aurimas
 * Date: 17.10.17
 * Time: 15.21
 */

namespace WeatherBundle\Utils;

use WeatherBundle\Exception\WeatherProviderException;
use WeatherBundle\Model\Location;
use WeatherBundle\Model\Weather;

class OpenWeatherMapWeatherProvider implements WeatherProviderInterface
{
    private const URLSTART = 'http://api.openweathermap.org/data/2.5/weather?';

    private $appId;

    public function __construct(string $appId)
    {
        $this->units = $this::UNITS_METRIC;

        $this->appId = $appId;
    }
    public function fetch(Location $location, int $units = 0): Weather
    {
        switch($units)
        {
            case $this::UNITS_METRIC:
                $unitsArray = [ 'units' => 'metric'];
                break;
            case $this::UNITS_IMPERIAL:
                $unitsArray = [ 'units' => 'imperial'];
                break;
            default:
                throw new WeatherProviderException('Invalid units argument.');
        }

        $parametersArray = [
            'lat' => $location->getLat(),
            'lon' => $location->getLon(),
            'appid' => $this->appId
        ];

        $parametersArray = array_merge($parametersArray, $unitsArray);

        $url = $this::URLSTART . http_build_query($parametersArray);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if ($result === false)
        {
            throw new WeatherProviderException('Weather provider is not accessible.');
        }

        return $this->parseWeather($result);
    }

    private function parseWeather($value)
    {
        $json = json_decode($value, true);
        if ($json === null)
        {
            throw new WeatherProviderException('Invalid provider response. Bad file format.');
        }

        $temperature = $json['main']['temp'];
        if (empty($temperature))
        {
            throw new WeatherProviderException('Invalid provider response. JSON file do not include required information.');
        }
        $pressure = $json['main']['pressure'];
        $humidity = $json['main']['humidity'];
        $windSpeed = $json['wind']['speed'];
        return new Weather($temperature, $pressure, $humidity, $windSpeed);
    }
}