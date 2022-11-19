<?php

namespace GislerCMS\Controller\Admin\Misc\User;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Filter\ToBool;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\Identical;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

/**
 * Class EditController
 * @package GislerCMS\Controller\Admin\Misc\User
 */
class EditController extends AbstractController
{
    const NAME = 'admin-misc-user-edit';
    const PATTERN = '{admin_route}/misc/user[/{id:[0-9]+}]';
    const METHODS = ['GET', 'POST'];

    const LANGUAGES = [
        'de' => 'Deutsch'
    ];

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
        $id = (int)$request->getAttribute('route')->getArgument('id');
        $cont = SessionHelper::getContainer();

        $user = new User();
        if ($id > 0) {
            $user = User::get($id);
        }
        $data = $user;

        $errors = [];
        $msg = false;
        if ($cont->offsetExists('user_saved')) {
            $cont->offsetUnset('user_saved');
            $msg = 'save_success';
        }

        if ($request->isPost()) {
            if (is_null($request->getParsedBodyParam('delete'))) {
                $data = $request->getParsedBody();
                $filter = $this->getInputFilter();
                $filter->setData($data);
                if (!$filter->isValid()) {
                    $errors = array_merge($errors, array_keys($filter->getMessages()));
                }
                $data = $filter->getValues();

                if (sizeof($errors) == 0) {
                    $user->setUsername($data['username']);
                    $user->setFirstname($data['firstname']);
                    $user->setLastname($data['lastname']);
                    $user->setEmail($data['email']);
                    $user->setLocale($data['locale']);
                    $user->setLocked($data['locked']);

                    if (!empty($data['password_confirm'])) {
                        $user->setPassword(password_hash($data['password_new'], PASSWORD_DEFAULT));
                    }

                    $res = $user->save();
                    if (is_null($res)) {
                        $msg = 'save_error';
                    } else {
                        $cont->offsetSet('user_saved', true);
                        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/misc/user/' . $res->getUserId());
                    }
                } else {
                    $msg = 'invalid_input';
                }
            } else {
                if ($user->delete()) {
                    return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route']);
                } else {
                    $msg = 'delete_error';
                }
            }
        }
        return $this->render($request, $response, 'admin/misc/user/edit.twig', [
            'languages' => self::LANGUAGES,
            'user' => $data,
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
            ],
            [
                'name' => 'password_new',
                'required' => false,
                'validators' => [
                    new NotEmpty(),
                    new StringLength([
                        'min' => 6
                    ])
                ]
            ],
            [
                'name' => 'password_confirm',
                'required' => false,
                'validators' => [
                    new NotEmpty(),
                    new StringLength([
                        'min' => 6
                    ]),
                    new Identical([
                        'token' => 'password_new'
                    ])
                ]
            ],
            [
                'name' => 'locked',
                'required' => false,
                'filters' => [
                    new ToBool()
                ]
            ]
        ]);
    }
}
