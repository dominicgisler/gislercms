<?php

namespace GislerCMS\Controller\Admin\Misc;

use GislerCMS\Controller\Admin\AdminAbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminMiscMigrationController
 * @package GislerCMS\Controller
 */
class AdminMiscMigrationController extends AdminAbstractController
{
    const NAME = 'admin-misc-migration';
    const PATTERN = '{admin_route}/misc/migration';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
//        MigrationHelper::executeMigrations($this->get('pdo'));
        return $this->render($request, $response, 'admin/misc/migration.twig', [
            'data' => []
        ]);
    }
}
