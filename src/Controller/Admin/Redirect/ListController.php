<?php

namespace GislerCMS\Controller\Admin\Redirect;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Redirect;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ListController
 * @package GislerCMS\Controller\Admin\Redirect
 */
class ListController extends AbstractController
{
    const NAME = 'admin-redirect-list';
    const PATTERN = '{admin_route}/redirect/list';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $redirects = Redirect::getAll();
        return $this->render($request, $response, 'admin/redirect/list.twig', [
            'redirects' => $redirects
        ]);
    }
}
