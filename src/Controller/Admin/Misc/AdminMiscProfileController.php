<?php

namespace GislerCMS\Controller\Admin\Misc;

use GislerCMS\Controller\Admin\AdminAbstractController;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\User;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\InputFilter\Factory;
use Zend\Validator\EmailAddress;
use Zend\Validator\InArray;
use Zend\Validator\StringLength;

/**
 * Class AdminMiscProfileController
 * @package GislerCMS\Controller
 */
class AdminMiscProfileController extends AdminAbstractController
{
    const NAME = 'admin-profile';
    const PATTERN = '{admin_route}/misc/profile';
    const METHODS = ['GET', 'POST'];

    const LANGUAGES = [
        'de' => 'Deutsch'
    ];

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
        $user = User::get($user->getUserId());

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
            $user->setUsername($data['username']);
            $user->setFirstname($data['firstname']);
            $user->setLastname($data['lastname']);
            $user->setEmail($data['email']);
            $user->setLocale($data['locale']);

            if (sizeof($errors) == 0) {
                $saveError = false;

                $res = $user->save();
                if (!is_null($res)) {
                    $user = $res;
                    $cont->offsetSet('user', $user);
                } else {
                    $saveError = true;
                }

                if ($saveError) {
                    $msg = 'save_error';
                } else {
                    $msg = 'save_success';
                }
            } else {
                $msg = 'invalid_input';
            }
        }
        return $this->render($request, $response, 'admin/misc/profile.twig', [
            'languages' => self::LANGUAGES,
            'data' => $user,
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
                'name' => 'username',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 3,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'firstname',
                'required' => false,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'lastname',
                'required' => false,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'email',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 1,
                        'max' => 255
                    ]),
                    new EmailAddress()
                ]
            ],
            [
                'name' => 'locale',
                'required' => false,
                'filters' => [],
                'validators' => [
                    new InArray([
                        'haystack' => array_keys(self::LANGUAGES)
                    ])
                ]
            ]
        ]);
    }
}
