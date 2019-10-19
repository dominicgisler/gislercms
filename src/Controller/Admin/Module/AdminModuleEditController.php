<?php

namespace GislerCMS\Controller\Admin\Module;

use GislerCMS\Controller\Admin\AdminAbstractController;
use GislerCMS\Controller\Module\AbstractModuleController;
use GislerCMS\Model\Config;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\InputFilter\Factory;

/**
 * Class AdminModuleEditController
 * @package GislerCMS\Controller\Admin\Module
 */
class AdminModuleEditController extends AdminAbstractController
{
    const NAME = 'admin-module-edit';
    const PATTERN = '{admin_route}/module/{name}';
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

        $conts = [];
        foreach (glob(__DIR__ . '/../../Module/*.php') as $file) {
            $class = basename($file, '.php');
            $cont = '\\GislerCMS\\Controller\\Module\\' . $class;
            if (is_subclass_of($cont, AbstractModuleController::class)) {
                $conts[] = $class;
            }
        }

        if (array_key_exists($name, $this->get('settings')['module'])) {
            $mod = $this->get('settings')['module'][$name];

            $errors = [];
            $msg = false;

            if ($request->isPost()) {
                $data = $request->getParsedBody();
                $filter = $this->getInputFilter();
                $filter->setData($data);
                if (!$filter->isValid()) {
                    $errors = array_merge($errors, array_keys($filter->getMessages()));
                }
                $data = $filter->getValues();

                if (sizeof($errors) == 0) {
                    $saveError = false;

                    if ($saveError) {
                        $msg = 'save_error';
                    } else {
                        $msg = 'save_success';
                        foreach (Config::getAll() as $config) {
                            $data[$config->getName()] = $config->getValue();
                        }
                    }
                } else {
                    $msg = 'invalid_input';
                }
            }

            return $this->render($request, $response, 'admin/module/edit.twig', [
                'module' => [
                    'name' => $name,
                    'controller' => $mod['controller'],
                    'config' => json_encode($mod['config'], JSON_PRETTY_PRINT)
                ],
                'controllers' => $conts,
                'message' => $msg,
                'errors' => $errors
            ]);
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
                'name' => 'controller',
                'required' => true,
                'filters' => [],
                'validators' => []
            ],
            [
                'name' => 'config',
                'required' => true,
                'filters' => [],
                'validators' => []
            ]
        ]);
    }
}
