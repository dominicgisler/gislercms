<?php

namespace GislerCMS\Controller\Admin\Module;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Controller\Module\AbstractModuleController;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Module;
use GislerCMS\Validator\ModuleControllerExists;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ManageController
 * @package GislerCMS\Controller\Admin\Module
 */
class ManageController extends AbstractController
{
    const NAME = 'admin-module-manage';
    const PATTERN = '{admin_route}/module/{id}';
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
        $id = $request->getAttribute('route')->getArgument('id');
        $conts = ModuleControllerExists::getModuleControllers();
        $this->setTrans();

        if (intval($id) > 0) {
            $mod = Module::get($id);
            if ($mod->getModuleId() == $id) {
                $errors = [];
                $msg = false;

                $cnt = SessionHelper::getContainer();
                if ($request->isPost()) {
                    if (!is_null($request->getParsedBodyParam('delete'))) {
                        if ($mod->delete()) {
                            $cnt->offsetSet('module_deleted', true);
                            return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/module/add');
                        } else {
                            $msg = 'delete_error';
                        }
                    }
                }

                $cfg = json_decode($mod->getConfig(), true);
                /** @var AbstractModuleController $cont */
                $cont = '\\GislerCMS\\Controller\\Module\\' . $mod->getController();
                $manageCont = $cont::getManageController();
                /** @var Manage\AbstractController $manageCont */
                $manageCont = new $manageCont($cfg, $this->get('view'));
                $modContent = $manageCont->manage($mod, $request);

                return $this->render($request, $response, 'admin/module/manage.twig', [
                    'module' => $mod,
                    'controllers' => $conts,
                    'message' => $msg,
                    'errors' => $errors,
                    'module_content' => $modContent
                ]);
            }
        }

        $modules = Module::getAll();
        return $this->render($request, $response, 'admin/module/not-found.twig', [
            'modules' => $modules
        ]);
    }
}
