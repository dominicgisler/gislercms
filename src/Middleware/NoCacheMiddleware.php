<?php

namespace GislerCMS\Middleware;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class NoCacheMiddleware
 * @package GislerCMS\Middleware
 */
class NoCacheMiddleware
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
     * @throws Exception
     */
    public function __invoke(Request|ServerRequestInterface $request, Response|ResponseInterface $response, callable $next): Response|ResponseInterface
    {
        return $next($request, $response->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Pragma', 'no-cache'));
    }
}
