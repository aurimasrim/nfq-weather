<?php

namespace WeatherBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WeatherBundle\Utils\AccuWeatherProvider;
use WeatherBundle\Utils\DelegatingWeatherProvider;
use WeatherBundle\Utils\WeatherProviderInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $weatherProvider = $this->container->get('weather.interface');

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => get_class($weatherProvider) . ' key='
        ]);
    }
}
