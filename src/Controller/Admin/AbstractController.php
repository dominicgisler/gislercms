<?php

namespace GislerCMS\Controller\Admin;

use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\Module;
use GislerCMS\Model\Page;
use GislerCMS\Model\User;
use GislerCMS\Model\Widget;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

/**
 * Class AbstractController
 * @package GislerCMS\Controller\Admin
 */
abstract class AbstractController
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
     * @throws \Exception
     */
    protected function render(Request $request, Response $response, string $template, array $data = []): Response
    {
        $cont = SessionHelper::getContainer();
        /** @var User $user */
        $user = $cont->offsetGet('user');

        $nav = $this->get('settings')['admin_navigation'];
        $nav = $this->replacePlaceholders($nav);

        /** @var Route $route */
        $route = $request->getAttribute('route');
        $arr = [
            'route' => $route->getName(),
            'admin_url' => $this->get('base_url') . $this->get('settings')['global']['admin_route'],
            'user' => $user,
            'navigation' => $nav
        ];
        return $this->get('view')->render($response, $template, array_merge($arr, $data));
    }

    /**
     * @param $val
     * @return mixed
     * @throws \Exception
     */
    private function replacePlaceholders($val)
    {
        if (is_string($val)) {
            $val = str_replace('{admin_url}', $this->get('base_url') . $this->get('settings')['global']['admin_route'], $val);
            $val = str_replace('{pages_count}', sizeof(Page::getAll()), $val);
            $val = str_replace('{pages_trash_count}', sizeof(Page::getTrash()), $val);
            $val = str_replace('{widgets_count}', sizeof(Widget::getAll()), $val);
            $val = str_replace('{widgets_trash_count}', sizeof(Widget::getTrash()), $val);
            $val = str_replace('{modules_count}', sizeof(Module::getAll()), $val);
        } elseif (is_iterable($val)) {
            foreach ($val as &$elem) {
                $elem = $this->replacePlaceholders($elem);
            }
        }
        return $val;
    }
}
