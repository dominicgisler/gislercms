<?php

namespace GislerCMS\Controller\Admin\Module\Manage;

use Exception;
use GislerCMS\Model\Module;
use GislerCMS\Validator\ValidJson;
use Slim\Http\Request;
use Slim\Views\Twig;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;

/**
 * Class AbstractController
 * @package GislerCMS\Admin\Module\Manage
 */
abstract class AbstractController
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var Twig
     */
    protected $view;

    /**
     * AbstractController constructor.
     * @param array $config
     * @param Twig $view
     */
    public function __construct(array $config, Twig $view)
    {
        $this->config = $config;
        $this->view = $view;
    }

    /**
     * @param Module $mod
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function manage(Module $mod, Request $request): string
    {
        $errors = [];
        $msg = false;

        if ($request->isPost()) {
            if (is_null($request->getParsedBodyParam('delete'))) {
                $data = $request->getParsedBody();
                $filter = $this->getInputFilter();
                $filter->setData($data);
                if (!$filter->isValid()) {
                    $errors = array_merge($errors, array_keys($filter->getMessages()));
                }
                $data = $filter->getValues();
                $mod->setConfig($data['config']);

                if (sizeof($errors) == 0) {
                    $res = $mod->save();
                    if (!is_null($res)) {
                        $msg = 'save_success';
                    } else {
                        $msg = 'save_error';
                    }
                } else {
                    $msg = 'invalid_input';
                }
            }
        }

        return $this->view->fetch('admin/module/manage/default.twig', [
            'module' => $mod,
            'errors' => $errors,
            'message' => $msg
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
