<?php

namespace GislerCMS\Controller\Admin\Misc\User;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\User;
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
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        return $this->render($request, $response, 'admin/misc/user/list.twig', [
            'users' => User::getAll()
        ]);
    }
}
