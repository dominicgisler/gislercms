<?php

namespace GislerCMS\Controller\Admin\Misc\System;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Filter\ToBool;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Config;
use GislerCMS\Model\Mailer;
use GislerCMS\Model\User;
use Laminas\Filter\ToInt;
use Slim\Http\Request;
use Slim\Http\Response;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;

/**
 * Class MailerController
 * @package GislerCMS\Controller\Admin\Misc\System
 */
class MailerController extends AbstractController
{
    const NAME = 'admin-misc-system-mailer';
    const PATTERN = '{admin_route}/misc/system/mailer';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $cont = SessionHelper::getContainer();
        $msg = false;
        $errors = [];
        $data = $this->get('settings')['mailer'];

        if ($request->isPost()) {
            $data = $request->getParsedBody();
            $filter = $this->getInputFilter();
            $filter->setData($data);
            if (!$filter->isValid()) {
                $errors = array_merge($errors, array_keys($filter->getMessages()));
            }
            $data = $filter->getValues();

            $saveError = false;
            foreach ($data as $key => $val) {
                $config = Config::getConfig('mailer', $key);
                if ($key != 'password' || !empty($val)) {
                    $config->setValue($val);
                    $res = $config->save();
                    if (is_null($res)) {
                        $saveError = true;
                    }
                }
                $data[$key] = $config->getValue();
            }

            if ($saveError) {
                $msg = 'save_error';
            } else if (!is_null($request->getParsedBodyParam('test'))) {
                /** @var User $user */
                $user = $cont->offsetGet('user');
                $user = User::get($user->getUserId());

                $subject = $this->get('view')->fetch('mailer/testmail-subject.twig');
                $message = $this->get('view')->fetch('mailer/testmail-body.twig', [
                    'user' => $user
                ]);

                $mailer = new Mailer($data);
                $mailer->addAddress($user->getEmail(), $user->getDisplayName());
                $mailer->Subject = $subject;
                $mailer->Body = $message;

                if ($mailer->send()) {
                    $msg = 'test_success';
                } else {
                    $msg = 'test_fail';
                    $errors['message'] = $mailer->ErrorInfo;
                }
            } else {
                $msg = 'save_success';
            }
        }
        return $this->render($request, $response, 'admin/misc/system/mailer.twig', [
            'data' => $data,
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
                'name' => 'smtp',
                'required' => false,
                'filters' => [
                    new ToBool()
                ],
                'validators' => []
            ],
            [
                'name' => 'smtpauth',
                'required' => false,
                'filters' => [
                    new ToBool()
                ],
                'validators' => []
            ],
            [
                'name' => 'host',
                'required' => true,
                'filters' => [],
                'validators' => []
            ],
            [
                'name' => 'username',
                'required' => true,
                'filters' => [],
                'validators' => []
            ],
            [
                'name' => 'password',
                'required' => false,
                'filters' => [],
                'validators' => []
            ],
            [
                'name' => 'smtpsecure',
                'required' => true,
                'filters' => [],
                'validators' => []
            ],
            [
                'name' => 'port',
                'required' => true,
                'filters' => [
                    new ToInt()
                ],
                'validators' => []
            ],
            [
                'name' => 'default_name',
                'required' => true,
                'filters' => [],
                'validators' => []
            ],
            [
                'name' => 'default_email',
                'required' => true,
                'filters' => [],
                'validators' => []
            ]
        ]);
    }
}
