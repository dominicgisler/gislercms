<?php

namespace GislerCMS\Controller\Admin\Misc;

use GislerCMS\Controller\Admin\AdminAbstractController;
use GislerCMS\Filter\ToBool;
use GislerCMS\Filter\ToLanguage;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Config;
use GislerCMS\Model\Language;
use GislerCMS\Model\User;
use GislerCMS\Validator\LanguageExists;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\InputFilter\Factory;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * Class AdminMiscConfigControllerAdmin
 * @package GislerCMS\Controller
 */
class AdminMiscConfigController extends AdminAbstractController
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
        $configs = Config::getAll();
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

            if (sizeof($errors) == 0) {
                $saveError = false;

//                foreach ($configs as &$config) {
//                    $res = $config->save();
//                    if (!is_null($res)) {
//                        $config = $res;
//                    } else {
//                        $saveError = true;
//                    }
//                }

                if ($saveError) {
                    $msg = 'save_error';
                } else {
                    $msg = 'save_success';
                }
            } else {
                $msg = 'invalid_input';
            }
        }
        return $this->render($request, $response, 'admin/misc/config.twig', [
            'languages' => $languages,
            'config' => $data,
            'message' => $msg
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
                'validators' => [
                    new NotEmpty(),
                    new StringLength([
                        'min' => 1,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'language',
                'required' => true,
                'filters' => [
                    new ToLanguage()
                ],
                'validators' => [
                    new LanguageExists()
                ]
            ],
            [
                'name' => 'enabled',
                'required' => false,
                'filters' => [
                    new ToBool()
                ],
                'validators' => []
            ]
        ]);
    }
}
