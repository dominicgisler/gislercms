<?php

namespace GislerCMS\Controller\Admin\Module;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Module;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ListController
 * @package GislerCMS\Controller\Admin\Module
 */
class ListController extends AbstractController
{
    const NAME = 'admin-module-list';
    const PATTERN = '{admin_route}/module/list';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $modules = Module::getAll();
        return $this->render($request, $response, 'admin/module/list.twig', [
            'modules' => $modules
        ]);
    }
}
