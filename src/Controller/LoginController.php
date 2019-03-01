<?php

namespace GislerCMS\Controller;

use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\User;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LoginController
 * @package GislerCMS\Controller
 */
class LoginController extends AbstractController
{
    const NAME = 'login';
    const PATTERN = '/login';
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
            $user = User::getUser($username);
            if ($user->getUserId() > 0) {
                if (password_verify($password, $user->getPassword())) {
                    $cont = SessionHelper::getContainer();
                    $cont->offsetSet('user', $user);
                    return $response->withRedirect($this->get('base_url'));
                }
            }

            $status = self::HTTP_FAIL;
            $data = [
                'error' => true,
                'username' => $username
            ];
        }

        return $this->render($request, $response, 'login.twig', $data);
    }
}
