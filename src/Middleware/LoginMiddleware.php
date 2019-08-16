<?php

namespace GislerCMS\Middleware;

use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LoginMiddleware
 * @package GislerCMS\Middleware
 */
class LoginMiddleware
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
     * @throws \Exception
     */
    public function __invoke($request, $response, $next)
    {
        $cont = SessionHelper::getContainer();

        if ($cont->offsetExists('user')) {
            /** @var User $user */
            $user = $cont->offsetGet('user');
            DbModel::init($this->container->get('pdo'));
            $dbUser = User::getByUsername($user->getUsername());

            if ($user->isEqual($dbUser)) {
                $dbUser->setLastActivity(date('Y-m-d H:i:s'));
                $dbUser->save();
                $cont->offsetSet('user', $dbUser);
                return $next($request, $response);
            }
        }

        return $response->withRedirect($this->container->get('base_url') . $this->container->get('settings')['global']['admin_route'] . '/login');
    }
}
