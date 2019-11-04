<?php

namespace GislerCMS\Controller\Admin\Auth;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\User;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\InputFilter\Factory;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * Class ResetController
 * @package GislerCMS\Controller\Admin\Auth
 */
class ResetController extends AbstractController
{
    const NAME = 'admin-reset';
    const PATTERN = '{admin_route}/reset/{key}';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $key = $request->getAttribute('route')->getArgument('key');

        $user = User::getWhere('`reset_key` = ?', [$key]);

        if ($user->getUserId() > 0) {
            $errors = [];
            $msg = '';

            if ($request->isPost()) {
                $data = $request->getParsedBody();
                $filter = $this->getInputFilter($user);
                $filter->setData($data);
                if (!$filter->isValid()) {
                    $errors = array_merge($errors, array_keys($filter->getMessages()));
                }
                $data = $filter->getValues();
                $user->setPassword(password_hash($data['password_new'], PASSWORD_DEFAULT));
                $user->setResetKey('');
                $user->setLocked(false);

                if (sizeof($errors) == 0) {
                    $saveError = false;

                    $res = $user->save();
                    if (is_null($res)) {
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

            $data = [
                'errors' => $errors,
                'message' => $msg
            ];

            return $this->render($request, $response, 'admin/auth/reset.twig', $data);
        }
        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route']);
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
