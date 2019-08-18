<?php

namespace GislerCMS\Controller\Admin\Misc;

use GislerCMS\Controller\Admin\AdminAbstractController;
use GislerCMS\Helper\MigrationHelper;
use GislerCMS\Model\Migration;
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
        return $this->render($request, $response, 'admin/misc/migration.twig', [
            'migrations' => $migrations,
            'error' => $error,
            'messages' => $messages
        ]);
    }
}
