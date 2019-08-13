<?php

namespace GislerCMS\Controller\Admin;

use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\Page;
use GislerCMS\Model\User;
use GislerCMS\Model\Widget;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

/**
 * Class AdminAbstractController
 * @package GislerCMS\Controller
 */
abstract class AdminAbstractController
{
    /**
     * @var \Slim\Container|\Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @param \Slim\Container|\Psr\Container\ContainerInterface $container
     * @param bool $initDBModel
     */
    public function __construct($container, $initDBModel = true)
    {
        $this->container = $container;
        if ($initDBModel) {
            DbModel::init($this->get('pdo'));
        }
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
        $cont = SessionHelper::getContainer();
        /** @var User $user */
        $user = $cont->offsetGet('user');

        /** @var Route $route */
        $route = $request->getAttribute('route');
        $arr = [
            'route' => $route->getName(),
            'admin_url' => $this->get('base_url') . $this->get('settings')['admin_route'],
            'pages' => Page::getAll(),
            'widgets' => Widget::getAll(),
            'user' => $user
        ];
        return $this->get('view')->render($response, $template, array_merge($arr, $data));
    }
}
