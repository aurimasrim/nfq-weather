<?php

namespace WeatherBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WeatherBundle\Model\Location;
use WeatherBundle\Utils\AccuWeatherProvider;
use WeatherBundle\Utils\DelegatingWeatherProvider;
use WeatherBundle\Utils\WeatherProviderInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, WeatherProviderInterface $weatherProvider)
    {
        return $this->render('default/index.html.twig', [
            'class_name' => get_class($weatherProvider),
            'weather' => $weatherProvider->fetch(new Location(55, 55))->getTemperature()
        ]);
    }
}
