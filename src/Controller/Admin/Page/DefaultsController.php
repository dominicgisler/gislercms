<?php

namespace GislerCMS\Controller\Admin\Page;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Filter\ToLanguage;
use GislerCMS\Model\Config;
use GislerCMS\Model\Language;
use GislerCMS\Model\Page;
use GislerCMS\Validator\LanguageExists;
use Slim\Http\Request;
use Slim\Http\Response;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\StringLength;

/**
 * Class DefaultsController
 * @package GislerCMS\Controller\Admin\Page
 */
class DefaultsController extends AbstractController
{
    const NAME = 'admin-page-defaults';
    const PATTERN = '{admin_route}/page/defaults';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $languages = Language::getAll();
        $configs = Config::getBySection('page');
        $data = [];
        foreach ($configs as $config) {
            $data[$config->getName()] = $config->getValue();
        }

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

                foreach ($configs as &$config) {
                    if (isset($data[$config->getName()])) {
                        $val = $data[$config->getName()];
                        if ($val instanceof Language) {
                            $val = $val->getLanguageId();
                        } elseif ($val instanceof Page) {
                            $val = $val->getPageId();
                        }
                        $config->setValue($val);
                        $res = $config->save();
                        if (!is_null($res)) {
                            $config = $res;
                        } else {
                            $saveError = true;
                        }
                    }
                }

                if ($saveError) {
                    $msg = 'save_error';
                } else {
                    $msg = 'save_success';
                    unset($config);
                    foreach (Config::getAll() as $config) {
                        $data[$config->getName()] = $config->getValue();
                    }
                }
            } else {
                $msg = 'invalid_input';
            }
        }
        return $this->render($request, $response, 'admin/page/defaults.twig', [
            'languages' => $languages,
            'config' => $data,
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
                'name' => 'meta_keywords',
                'required' => false,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 512
                    ])
                ]
            ],
            [
                'name' => 'meta_description',
                'required' => false,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 512
                    ])
                ]
            ],
            [
                'name' => 'meta_author',
                'required' => false,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 255
                    ])
                ]
            ],
            [
                'name' => 'meta_copyright',
                'required' => false,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 255
                    ])
                ]
            ],
            [
                'name' => 'default_language',
                'required' => true,
                'filters' => [
                    new ToLanguage()
                ],
                'validators' => [
                    new LanguageExists()
                ]
            ]
        ]);
    }
}
