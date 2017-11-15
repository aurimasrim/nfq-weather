<?php
/**
 * Created by PhpStorm.
 * User: aurimas
 * Date: 17.10.17
 * Time: 15.09
 */

namespace WeatherBundle\Model;


class Location
{
    private $lon, $lat;

    public function __construct(float $lon, float $lat)
    {
        $this->lon = $lon;
        $this->lat = $lat;
    }


    public function getLon(): float
    {
        return $this->lon;
    }

    public function getLat(): float
    {
        return $this->lat;
    }
}