<?php

namespace GislerCMS\Controller;

use GislerCMS\Model\DbModel;
use GislerCMS\Model\Widget;
use GislerCMS\Model\WidgetTranslation;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

/**
 * Class AbstractController
 * @package GislerCMS\Controller
 */
abstract class AbstractController
{
    /**
     * @var \Slim\Container|\Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @param \Slim\Container|\Psr\Container\ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        DbModel::init($this->get('pdo'));
    }

    /**
     * @param string $var
     * @return mixed
     */
    protected function get($var)
    {
        return $this->container->get($var);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $template
     * @param array $data
     * @return Response
     */
    protected function render(Request $request, Response $response, string $template, array $data = []): Response
    {
        /** @var Route $route */
        $route = $request->getAttribute('route');
        $arr = [
            'route' => $route->getName(),
            'widget' => new Widget(),
            'widget_translation' => new WidgetTranslation()
        ];
        return $this->get('view')->render($response, $template, array_merge($arr, $data));
    }
}
