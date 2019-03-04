<?php

namespace GislerCMS\Controller;

use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\User;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminLoginController
 * @package GislerCMS\Controller
 */
class AdminLoginController extends AbstractController
{
    const NAME = 'admin-login';
    const PATTERN = '{admin_route}/login';
    const METHODS = ['GET', 'POST'];

    const HTTP_OK = 200;
    const HTTP_FAIL = 403;

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
            $user = User::getByUsername($username);
            if ($user->getUserId() > 0) {
                if (password_verify($password, $user->getPassword())) {
                    $cont = SessionHelper::getContainer();
                    $cont->offsetSet('user', $user);
                    return $response->withRedirect($this->get('base_url') . $this->get('settings')['admin_route']);
                }
            }

            $status = self::HTTP_FAIL;
            $data = [
                'error' => true,
                'username' => $username
            ];
        }

        return $this->render($request, $response, 'admin/login.twig', $data);
    }
}
