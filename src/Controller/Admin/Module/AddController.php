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
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\StringLength;

/**
 * Class AddController
 * @package GislerCMS\Controller\Admin\Module
 */
class AddController extends AbstractController
{
    const NAME = 'admin-module-add';
    const PATTERN = '{admin_route}/module/add';
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

        if (intval($id) > 0) {
            $mod = Module::get($id);
        } else {
            $mod = new Module();
        }

        $errors = [];
        $msg = false;

        $cnt = SessionHelper::getContainer();
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
                    $mod->setName($data['name']);
                    $mod->setController($data['controller']);
                    $mod->setEnabled(true);

                    /** @var AbstractModuleController $cont */
                    $cont = '\\GislerCMS\\Controller\\Module\\' . $data['controller'];
                    $mod->setConfig(json_encode($cont::getExampleConfig(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

                    $res = $mod->save();
                    if (!is_null($res)) {
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
                        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/module');
                    } else {
                        $msg = 'delete_error';
                    }
                }
            }
        }

        return $this->render($request, $response, 'admin/module/add.twig', [
            'module' => $mod,
            'controllers' => $conts,
            'message' => $msg,
            'errors' => $errors
        ]);
    }

    /**
     * @return InputFilterInterface
     */
    private function getInputFilter(): InputFilterInterface
    {
        $factory = new Factory();
        return $factory->createInputFilter([
            [
                'name' => 'name',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 1,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'controller',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new ModuleControllerExists()
                ]
            ]
        ]);
    }
}
