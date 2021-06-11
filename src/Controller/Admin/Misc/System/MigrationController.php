<?php

namespace GislerCMS\Controller\Admin\Misc\System;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Helper\MigrationHelper;
use GislerCMS\Model\Migration;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class MigrationController
 * @package GislerCMS\Controller\Admin\Misc\System
 */
class MigrationController extends AbstractController
{
    const NAME = 'admin-misc-system-migration';
    const PATTERN = '{admin_route}/misc/system/migration';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $error = false;
        $messages = [];
        if ($request->isPost() && !is_null($request->getParsedBodyParam('update'))) {
            $res = MigrationHelper::executeMigrations($this->get('pdo'));
            $error = ($res['status'] === 'error');
            $messages = $res['migrations'];
        }
        $migrations = MigrationHelper::getMigrations();
        foreach (Migration::getAll() as $mig) {
            $migrations[$mig->getName()]['done'] = $mig->getCreatedAt();
            $migrations[$mig->getName()]['description'] = $mig->getDescription();
        }
        return $this->render($request, $response, 'admin/misc/system/migration.twig', [
            'migrations' => $migrations,
            'error' => $error,
            'messages' => $messages
        ]);
    }
}
