<?php

namespace GislerCMS\Controller\Admin\Module;

use GislerCMS\Controller\Admin\AdminAbstractController;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Config;
use GislerCMS\Validator\ModuleControllerExists;
use GislerCMS\Validator\ValidJson;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\InputFilter\Factory;
use Zend\Validator\StringLength;

/**
 * Class AdminModuleEditController
 * @package GislerCMS\Controller\Admin\Module
 */
class AdminModuleEditController extends AdminAbstractController
{
    const NAME = 'admin-module-edit';
    const PATTERN = '{admin_route}/module[/{name}]';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $name = $request->getAttribute('route')->getArgument('name');
        $conts = ModuleControllerExists::getModuleControllers();

        if (array_key_exists($name, $this->get('settings')['module'])) {
            $mod = $this->get('settings')['module'][$name];
            $data = [
                'name' => $name,
                'controller' => $mod['controller'],
                'config' => json_encode($mod['config'], JSON_PRETTY_PRINT)
            ];
        } else {
            $data = [
                'new' => true,
                'name' => $name,
                'controller' => '',
                'config' => json_encode([], JSON_PRETTY_PRINT)
            ];
        }

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
                    $value = [
                        'controller' => $data['controller'],
                        'config' => json_decode($data['config'], true)
                    ];

                    $cfg = Config::getWhere('section = "module" AND name = ?', [$name]);
                    if (sizeof($cfg) > 0) {
                        $elem = $cfg[0];
                    } else {
                        $elem = new Config();
                        $elem->setSection('module');
                        $elem->setType('json');
                    }

                    $elem->setName($data['name']);
                    $elem->setValue($value);

                    $res = $elem->save();
                    if (!is_null($res)) {
                        $cnt->offsetSet('module_saved', true);
                        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/module/' . $elem->getName());
                    } else {
                        $msg = 'save_error';
                    }
                } else {
                    $msg = 'invalid_input';
                }
            } else {
                $cfg = Config::getWhere('section = "module" AND name = ?', [$name]);
                if (sizeof($cfg) > 0) {
                    $elem = $cfg[0];
                    if ($elem->delete()) {
                        $cnt->offsetSet('module_deleted', true);
                        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/module');
                    } else {
                        $msg = 'delete_error';
                    }
                }
            }
        }

        return $this->render($request, $response, 'admin/module/edit.twig', [
            'module' => $data,
            'controllers' => $conts,
            'message' => $msg,
            'errors' => $errors
        ]);
    }

    /**
     * @return \Zend\InputFilter\InputFilterInterface
     */
    private function getInputFilter()
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
            ],
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
