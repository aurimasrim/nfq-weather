<?php
/**
 * Created by PhpStorm.
 * User: aurimas
 * Date: 17.10.17
 * Time: 15.17
 */

namespace Nfq\Weather;


interface WeatherProviderInterface
{
    public const UNITS_METRIC = 0;
    public const UNITS_IMPERIAL = 1;

    public function fetch(Location $location, int $units = 0): Weather;
}