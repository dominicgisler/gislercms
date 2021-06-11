<?php

namespace GislerCMS\Controller\Admin\Auth;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Mailer;
use GislerCMS\Model\User;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ForgotPasswordController
 * @package GislerCMS\Controller\Admin\Auth
 */
class ForgotPasswordController extends AbstractController
{
    const NAME = 'admin-forgot-password';
    const PATTERN = '{admin_route}/forgot-password';
    const METHODS = ['GET', 'POST'];

    const TOKEN_LENGTH = 128;

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $data = [];

        if ($request->isPost()) {
            $username = $request->getParsedBodyParam('username');

            $error = false;
            $user = User::getByUsername($username);
            if ($user->getUserId() > 0) {
                $user->setResetKey($this->getToken());

                $adminURL = $this->get('base_url') . $this->get('settings')['global']['admin_route'];
                $subject = $this->get('view')->fetch('mailer/forgot-password-subject.twig');
                $message = $this->get('view')->fetch('mailer/forgot-password-body.twig', [
                    'user' => $user,
                    'admin_url' => $adminURL,
                    'reset_url' => $adminURL . '/reset/' . $user->getResetKey()
                ]);

                $mailer = new Mailer($this->get('settings')['mailer']);
                $mailer->addAddress($user->getEmail(), $user->getDisplayName());
                $mailer->Subject = $subject;
                $mailer->Body = $message;

                if ($user->save() && $mailer->send()) {
                    $msg = 'success';
                } else {
                    $user->setResetKey('');
                    $user->save();
                    $msg = 'send_error';
                    $error = true;
                }
            } else {
                $msg = 'invalid_input';
                $error = true;
            }

            $data = [
                'error' => $error,
                'message' => $msg,
                'username' => $username
            ];
        }

        return $this->render($request, $response, 'admin/auth/forgot-password.twig', $data);
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getToken(): string
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < self::TOKEN_LENGTH; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }
}
