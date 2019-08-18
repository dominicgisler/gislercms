<?php

namespace GislerCMS\Controller\Admin\Auth;

use GislerCMS\Controller\Admin\AdminAbstractController;
use GislerCMS\Model\Mailer;
use GislerCMS\Model\User;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminForgotPasswordController
 * @package GislerCMS\Controller
 */
class AdminForgotPasswordController extends AdminAbstractController
{
    const NAME = 'admin-forgot-password';
    const PATTERN = '{admin_route}/forgot-password';
    const METHODS = ['GET', 'POST'];

    const MESSAGE = 'Hallo %s' . PHP_EOL . PHP_EOL .
    'Jemand hat auf %s ein neues Passwort für diesen Account angefordert.' . PHP_EOL .
    'Bitte klicke auf folgenden Link um dein Passwort zu ändern:' . PHP_EOL .
    '%s' . PHP_EOL . PHP_EOL .
    'Falls du dein Passwort nicht zurücksetzen willst kannst du diese Nachricht ignorieren, dein Passwort bleibt dabei unverändert.' . PHP_EOL . PHP_EOL .
    'Liebe Grüsse' . PHP_EOL .
    'GislerCMS';

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $data = [];

        if ($request->isPost()) {
            $username = $request->getParsedBodyParam('username');

            $error = false;
            $msg = '';
            $user = User::getByUsername($username);
            if ($user->getUserId() > 0) {
                $user->setResetKey($this->getToken(128));

                $mailer = new Mailer($this->get('settings')['mailer']);
                $mailer->addAddress($user->getEmail(), $user->getDisplayName());
                $mailer->CharSet = 'UTF-8';
                $mailer->Subject = 'Passwort zurücksetzen';

                $adminURL = $this->get('base_url') . $this->get('settings')['global']['admin_route'];
                $mailer->Body = sprintf(self::MESSAGE, $user->getDisplayName(), $adminURL, $adminURL . '/reset/' . $user->getResetKey());

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
     * @param int $length
     * @return string
     * @throws \Exception
     */
    private function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }
}
