<?php

namespace GislerCMS\Middleware;

use Exception;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class NoLoginMiddleware
 * @package GislerCMS\Middleware
 */
class NoLoginMiddleware
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request|ServerRequestInterface $request PSR7 request
     * @param Response|ResponseInterface $response PSR7 response
     * @param callable $next Next middleware
     *
     * @return ResponseInterface|Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(Request|ServerRequestInterface $request, Response|ResponseInterface $response, callable $next): Response|ResponseInterface
    {
        $cont = SessionHelper::getContainer();

        if ($cont->offsetExists('user')) {
            /** @var User $user */
            $user = $cont->offsetGet('user');
            DbModel::init($this->container->get('pdo'));
            $dbUser = User::getByUsername($user->getUsername());

            if ($user->isEqual($dbUser)) {
                return $response->withRedirect($this->container->get('base_url') . $this->container->get('settings')['global']['admin_route']);
            }
        }

        $cont->offsetUnset('user');
        return $next($request, $response);
    }
}
