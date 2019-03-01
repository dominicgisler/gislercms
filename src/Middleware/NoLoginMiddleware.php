<?php

namespace GislerCMS\Middleware;

use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\User;
use Psr\Container\ContainerInterface;
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
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param  ServerRequestInterface|Request $request  PSR7 request
     * @param  ResponseInterface|Response     $response PSR7 response
     * @param  callable                       $next     Next middleware
     *
     * @return ResponseInterface|Response
     */
    public function __invoke($request, $response, $next)
    {
        $cont = SessionHelper::getContainer();

        if ($cont->offsetExists('user')) {
            /** @var User $user */
            $user = $cont->offsetGet('user');
            $dbUser = User::getUser($this->container->get('pdo'), $user->getUsername());

            if ($user->isEqual($dbUser)) {
                return $response->withRedirect($this->container->get('base_url'));
            }
        }

        $cont->offsetUnset('user');
        return $next($request, $response);
    }
}