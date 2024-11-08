<?php

namespace GislerCMS\Controller\Admin;

use Exception;
use GislerCMS\Application;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\Module;
use GislerCMS\Model\Page;
use GislerCMS\Model\Post;
use GislerCMS\Model\Redirect;
use GislerCMS\Model\User;
use GislerCMS\Model\Widget;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Container;
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
     * @var Container|ContainerInterface
     */
    protected Container|ContainerInterface $container;

    /**
     * @param ContainerInterface|Container $container
     * @param bool $initDBModel
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface|Container $container, bool $initDBModel = true)
    {
        if (PHP_SAPI == "cli") {
            exit;
        }
        $this->container = $container;
        if ($initDBModel) {
            DbModel::init($this->get('pdo'));
        }
    }

    /**
     * @param string $var
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function get(string $var): mixed
    {
        return $this->container->get($var);
    }

    /**
     * @return User|null
     */
    protected function setTrans(): ?User
    {
        $cont = SessionHelper::getContainer();
        /** @var User $user */
        $user = $cont->offsetGet('user');
        if ($user) {
            Application::setTransLocale($user->getLocale());
        } else {
            $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            Application::setTransLocale($locale);
        }
        return $user;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $template
     * @param array $data
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    protected function render(Request $request, Response $response, string $template, array $data = []): Response
    {
        $user = $this->setTrans();

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
        return $this->get('view')->render(
            $response,
            $template,
            array_merge($arr, $data)
        );
    }

    /**
     * @param $val
     * @return array|mixed|string|string[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function replacePlaceholders($val): mixed
    {
        if (is_string($val)) {
            $val = str_replace('{admin_url}', $this->get('base_url') . $this->get('settings')['global']['admin_route'], $val);
            $val = str_replace('{pages_count}', Page::countAvailable(), $val);
            $val = str_replace('{posts_count}', Post::countAvailable(), $val);
            $val = str_replace('{widgets_count}', Widget::countAvailable(), $val);
            $val = str_replace('{modules_count}', Module::countAll(), $val);
            $val = str_replace('{redirects_count}', Redirect::countAll(), $val);
            $val = str_replace('{trash_count}', Page::countTrash() + Post::countTrash() + Widget::countTrash(), $val);
        } elseif (is_iterable($val)) {
            foreach ($val as &$elem) {
                $elem = $this->replacePlaceholders($elem);
            }
        }
        return $val;
    }
}
