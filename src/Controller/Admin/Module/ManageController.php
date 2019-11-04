<?php

namespace GislerCMS\Controller\Admin\Module;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Module;
use GislerCMS\Validator\ModuleControllerExists;
use GislerCMS\Validator\ValidJson;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\InputFilter\Factory;

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
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $id = $request->getAttribute('route')->getArgument('id');
        $conts = ModuleControllerExists::getModuleControllers();

        if (intval($id) > 0) {
            $mod = Module::get($id);
            if ($mod->getModuleId() == $id) {
                $errors = [];
                $msg = false;

                $cnt = SessionHelper::getContainer();
                if ($cnt->offsetExists('module_saved')) {
                    $cnt->offsetUnset('module_saved');
                    $msg = 'save_success';
                }
                if ($cnt->offsetExists('module_deleted')) {
                    $cnt->offsetUnset('module_deleted');
                    $msg = 'delete_success';
                }

                if ($request->isPost()) {
                    if (is_null($request->getParsedBodyParam('delete'))) {
                        $data = $request->getParsedBody();
                        $filter = $this->getInputFilter();
                        $filter->setData($data);
                        if (!$filter->isValid()) {
                            $errors = array_merge($errors, array_keys($filter->getMessages()));
                        }
                        $data = $filter->getValues();

                        if (sizeof($errors) == 0) {
                            $mod->setConfig($data['config']);

                            $res = $mod->save();
                            if (!is_null($res)) {
                                $cnt->offsetSet('module_saved', true);
                                return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/module/' . $res->getModuleId());
                            } else {
                                $msg = 'save_error';
                            }
                        } else {
                            $msg = 'invalid_input';
                        }
                    } else {
                        if ($mod->getModuleId() > 0) {
                            if ($mod->delete()) {
                                $cnt->offsetSet('module_deleted', true);
                                return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/module/add');
                            } else {
                                $msg = 'delete_error';
                            }
                        }
                    }
                }

                return $this->render($request, $response, 'admin/module/manage.twig', [
                    'module' => $mod,
                    'controllers' => $conts,
                    'message' => $msg,
                    'errors' => $errors
                ]);
            }
        }
        return $this->render($request, $response, 'admin/module/not-found.twig');
    }

    /**
     * @return \Zend\InputFilter\InputFilterInterface
     */
    private function getInputFilter()
    {
        $factory = new Factory();
        return $factory->createInputFilter([
            [
                'name' => 'config',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new ValidJson()
                ]
            ]
        ]);
    }
}
