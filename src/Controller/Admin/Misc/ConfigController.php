<?php

namespace GislerCMS\Controller\Admin\Misc;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Filter\ToBool;
use GislerCMS\Filter\ToPage;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Config;
use GislerCMS\Model\Language;
use GislerCMS\Model\Page;
use GislerCMS\Model\User;
use GislerCMS\Validator\DoesNotContain;
use GislerCMS\Validator\PageExists;
use GislerCMS\Validator\StartsWith;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\InputFilter\Factory;
use Zend\Validator\Between;
use Zend\Validator\StringLength;

/**
 * Class ConfigController
 * @package GislerCMS\Controller\Admin\Misc
 */
class ConfigController extends AbstractController
{
    const NAME = 'admin-misc-config';
    const PATTERN = '{admin_route}/misc/config';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $cont = SessionHelper::getContainer();
        /** @var User $user */
        $user = $cont->offsetGet('user');

        $languages = Language::getAll();
        $configs = Config::getBySection('global');
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
                    foreach (Config::getAll() as $config) {
                        $data[$config->getName()] = $config->getValue();
                    }
                }
            } else {
                $msg = 'invalid_input';
            }
        }
        return $this->render($request, $response, 'admin/misc/config.twig', [
            'languages' => $languages,
            'config' => $data,
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
                'name' => 'maintenance_mode',
                'required' => false,
                'filters' => [
                    new ToBool()
                ],
                'validators' => []
            ],
            [
                'name' => 'default_page',
                'required' => true,
                'filters' => [
                    new ToPage()
                ],
                'validators' => [
                    new PageExists()
                ]
            ],
            [
                'name' => 'admin_route',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new StartsWith('/'),
                    new DoesNotContain('//'),
                    new StringLength([
                        'min' => 0,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'max_failed_logins',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new Between([
                        'min' => 1,
                        'max' => 1000
                    ])
                ]
            ]
        ]);
    }
}
