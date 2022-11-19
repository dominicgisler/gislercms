<?php

namespace GislerCMS\Controller\Admin\Misc\User;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ListController
 * @package GislerCMS\Controller\Admin\Misc\User
 */
class ListController extends AbstractController
{
    const NAME = 'admin-misc-user-list';
    const PATTERN = '{admin_route}/misc/user/list';
    const METHODS = ['GET', 'POST'];

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
        return $this->render($request, $response, 'admin/misc/user/list.twig', [
            'users' => User::getAll()
        ]);
    }
}
