<?php

namespace GislerCMS\Controller\Admin\Misc;

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
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\InputFilter\Factory;
use Zend\Validator\Between;
use Zend\Validator\StringLength;

/**
 * Class SystemController
 * @package GislerCMS\Controller\Admin\Misc
 */
class SystemController extends AbstractController
{
    const NAME = 'admin-misc-system';
    const PATTERN = '{admin_route}/misc/system';
    const METHODS = ['GET', 'POST'];

    const RELEASE_URL = 'https://api.github.com/repos/dominicgisler/gislercms/releases/latest';

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
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
                    return $response->withRedirect($this->get('base_url') . $data['admin_route'] . '/misc/config');
                }
            } else {
                $msg = 'invalid_input';
            }
        }
        return $this->render($request, $response, 'admin/misc/config.twig', [
            'sysinfo' => $this->getSysInfo(),
            'languages' => $languages,
            'pages' => Page::getAll(),
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
            ]
        ]);
    }

    /**
     * @return array[]
     */
    private function getSysInfo()
    {
        $cmsVersion = $this->get('settings')['version'];

        $update = [];
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP'
                ]
            ]
        ]);
        $content = file_get_contents(self::RELEASE_URL, false, $context);
        $release = json_decode($content, true);
        if (!empty($release['tag_name']) && $release['tag_name'] != $cmsVersion) {
            $update = [
                'current' => $cmsVersion,
                'latest' => $release['tag_name']
            ];
        }

        $data = [
            'CMS Version' => $cmsVersion,
            'PHP Version' => phpversion(),
            'MySQL Version' => $this->get('pdo')->query('select version()')->fetchColumn(),
            'Webserver' => $_SERVER['SERVER_SOFTWARE'],
            'URL' => $this->get('base_url'),
            'Verwaltungs-URL' => $this->get('base_url') . $this->get('settings')['global']['admin_route'],
            'Verzeichnis' => $_SERVER['DOCUMENT_ROOT'],
            'PHP Erweiterungen' => '- ' . join('<br>- ', get_loaded_extensions()),
            'HTTP Protokoll' => $_SERVER['SERVER_PROTOCOL'],
            'Servername' => $_SERVER['SERVER_NAME'],
            'Serveradresse' => $_SERVER['SERVER_ADDR'],
            'Serverport' => $_SERVER['SERVER_PORT'],
            'Serversignatur' => $_SERVER['SERVER_SIGNATURE']
        ];

        return [
            'data' => $data,
            'update' => $update
        ];
    }
}
