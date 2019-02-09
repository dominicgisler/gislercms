<?php

namespace GislerCMS\Controller;

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
    }

    /**
     * @param string $var
     * @return mixed
     */
    protected function get($var)
    {
        return $this->container->get($var);
    }
}