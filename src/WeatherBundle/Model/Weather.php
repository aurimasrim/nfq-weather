<?php
/**
 * Created by PhpStorm.
 * User: aurimas
 * Date: 17.10.17
 * Time: 15.15
 */

namespace WeatherBundle\Model;

class Weather
{
    private $temperature;
    private $pressure;
    private $humidity;
    private $windSpeed;

    /**
     * Weather constructor.
     * @param $temperature
     * @param $pressure
     * @param $humidity
     * @param $windSpeed
     */
    public function __construct($temperature, $pressure, $humidity, $windSpeed)
    {
        $this->temperature = $temperature;
        $this->pressure = $pressure;
        $this->humidity = $humidity;
        $this->windSpeed = $windSpeed;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function getPressure() : float
    {
        return $this->pressure;
    }

    public function getHumidity() : float
    {
        return $this->humidity;
    }

    public function getWindSpeed() : float
    {
        return $this->windSpeed;
    }
}