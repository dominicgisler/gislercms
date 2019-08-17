<?php

namespace GislerCMS\Controller\Admin\Misc;

use GislerCMS\Controller\Admin\AdminAbstractController;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\User;
use GislerCMS\Validator\PasswordVerify;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\InputFilter\Factory;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * Class AdminMiscChangePasswordController
 * @package GislerCMS\Controller
 */
class AdminMiscChangePasswordController extends AdminAbstractController
{
    const NAME = 'admin-misc-change-password';
    const PATTERN = '{admin_route}/misc/change-password';
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
        $user = User::get($user->getUserId());

        $errors = [];
        $msg = false;

        if ($request->isPost()) {
            $data = $request->getParsedBody();
            $filter = $this->getInputFilter($user);
            $filter->setData($data);
            if (!$filter->isValid()) {
                $errors = array_merge($errors, array_keys($filter->getMessages()));
            }
            $data = $filter->getValues();
            $user->setPassword(password_hash($data['password_new'], PASSWORD_DEFAULT));

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
        return $this->render($request, $response, 'admin/misc/change-password.twig', [
            'data' => $user,
            'message' => $msg,
            'errors' => $errors
        ]);
    }

    /**
     * @param User $user
     * @return \Zend\InputFilter\InputFilterInterface
     */
    private function getInputFilter(User $user)
    {
        $factory = new Factory();
        return $factory->createInputFilter([
            [
                'name' => 'password',
                'required' => true,
                'validators' => [
                    new NotEmpty(),
                    new PasswordVerify($user->getPassword())
                ]
            ],
            [
                'name' => 'password_new',
                'required' => true,
                'validators' => [
                    new NotEmpty(),
                    new StringLength([
                        'min' => 6
                    ])
                ]
            ],
            [
                'name' => 'password_confirm',
                'required' => true,
                'validators' => [
                    new NotEmpty(),
                    new StringLength([
                        'min' => 6
                    ]),
                    new Identical([
                        'token' => 'password_new'
                    ])
                ]
            ]
        ]);
    }
}
