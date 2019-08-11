<?php

namespace GislerCMS\Controller\Admin\Auth;

use GislerCMS\Controller\Admin\AdminAbstractController;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\User;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminLoginControllerAdmin
 * @package GislerCMS\Controller
 */
class AdminLoginController extends AdminAbstractController
{
    const NAME = 'admin-login';
    const PATTERN = '{admin_route}/login';
    const METHODS = ['GET', 'POST'];

    const HTTP_OK = 200;
    const HTTP_FAIL = 401;

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $status = self::HTTP_OK;
        $data = [];

        if ($request->isPost()) {
            $username = $request->getParsedBodyParam('username');
            $password = $request->getParsedBodyParam('password');

            DbModel::init($this->get('pdo'));
            $user = User::getByUsername($username, 'locked = 0');
            if ($user->getUserId() > 0) {
                if (password_verify($password, $user->getPassword())) {
                    $user->setFailedLogins(0);
                    $user->setLastLogin(date('Y-m-d H:i:s'));
                    $user = $user->save();
                    $cont = SessionHelper::getContainer();
                    $cont->offsetSet('user', $user);
                    return $response->withRedirect($this->get('base_url') . $this->get('settings')['admin_route']);
                } else {
                    $user->setFailedLogins($user->getFailedLogins() + 1);
                    if ($user->getFailedLogins() >= $this->get('settings')['max_failed_logins']) {
                        $user->setLocked(true);
                    }
                    $user->save();
                }
            }

            $status = self::HTTP_FAIL;
            $data = [
                'error' => true,
                'username' => $username
            ];
        }

        return $this->render($request, $response->withStatus($status), 'admin/login.twig', $data);
    }
}
