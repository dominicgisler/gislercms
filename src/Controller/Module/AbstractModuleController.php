<?php

namespace GislerCMS\Controller\Module;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;

/**
 * Class AbstractModuleController
 * @package GislerCMS\Controller\Module
 */
abstract class AbstractModuleController
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var Twig
     */
    protected $view;

    /**
     * AbstractModuleController constructor.
     * @param array $config
     * @param Twig $view
     */
    public function __construct($config, $view)
    {
        $this->config = $config;
        $this->view = $view;
    }

    /**
     * Method called on GET-Request
     *
     * @param Request $request
     * @param Response $response
     * @return string
     * @throws LoaderError
     */
    public function onGet($request, $response)
    {
        return $this->view->fetch('module/not-implemented.twig');
    }

    /**
     * Method called on POST-Request
     *
     * @param Request $request
     * @param Response $response
     * @return string
     * @throws LoaderError
     */
    public function onPost($request, $response)
    {
        return $this->view->fetch('module/not-implemented.twig');
    }
}
