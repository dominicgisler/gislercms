<?php

namespace GislerCMS\Controller\Admin\Misc;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\User;
use GislerCMS\Validator\PasswordVerify;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\Identical;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

/**
 * Class ChangePasswordController
 * @package GislerCMS\Controller\Admin\Misc
 */
class ChangePasswordController extends AbstractController
{
    const NAME = 'admin-misc-change-password';
    const PATTERN = '{admin_route}/misc/change-password';
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
     * @return InputFilterInterface
     */
    private function getInputFilter(User $user): InputFilterInterface
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
