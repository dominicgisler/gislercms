<?php

namespace GislerCMS\Controller\Admin\Misc\System;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Filter\ToBool;
use GislerCMS\Filter\ToPage;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Config;
use GislerCMS\Model\Language;
use GislerCMS\Model\Page;
use GislerCMS\Validator\DoesNotContain;
use GislerCMS\Validator\PageExists;
use GislerCMS\Validator\StartsWith;
use Laminas\Validator\InArray;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\Between;
use Laminas\Validator\StringLength;

/**
 * Class ConfigController
 * @package GislerCMS\Controller\Admin\Misc\System
 */
class ConfigController extends AbstractController
{
    const NAME = 'admin-misc-system-config';
    const PATTERN = '{admin_route}/misc/system/config';
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
        $cont = SessionHelper::getContainer();

        $languages = Language::getAll();
        $configs = Config::getBySection('global');
        $data = [];
        foreach ($configs as $config) {
            $data[$config->getName()] = $config->getValue();
        }

        $errors = [];
        $msg = false;
        if ($cont->offsetExists('config_saved')) {
            $cont->offsetUnset('config_saved');
            $msg = 'save_success';
        }

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
                    $cont->offsetSet('config_saved', true);
                    return $response->withRedirect($this->get('base_url') . $data['admin_route'] . '/misc/system/config');
                }
            } else {
                $msg = 'invalid_input';
            }
        }
        return $this->render($request, $response, 'admin/misc/system/config.twig', [
            'languages' => $languages,
            'pages' => Page::getAll(),
            'config' => $data,
            'message' => $msg,
            'errors' => $errors,
            'docroot' => $_SERVER['DOCUMENT_ROOT']
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
                'name' => 'maintenance_mode',
                'required' => false,
                'filters' => [
                    new ToBool()
                ],
                'validators' => []
            ],
            [
                'name' => 'enable_tracking',
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
            ],
            [
                'name' => 'interval_stats_refresh',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new InArray([
                        'haystack' => [0, 1, 3, 6, 12, 24, 168, 720]
                    ])
                ]
            ],
            [
                'name' => 'interval_backup',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new InArray([
                        'haystack' => [0, 1, 3, 6, 12, 24, 168, 720]
                    ])
                ]
            ],
            [
                'name' => 'backup_count',
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
