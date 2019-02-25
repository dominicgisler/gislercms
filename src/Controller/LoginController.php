<?php

namespace GislerCMS\Controller;

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
     */
    public function __invoke($request, $response)
    {
        $status = self::HTTP_OK;
        $data = [];

        if ($request->isPost()) {
            $username = $request->getParsedBodyParam('username');
            $password = $request->getParsedBodyParam('password');

            $pdo = new \PDO('mysql:host=localhost;dbname=gislercms;port=3306', 'root', 'root');
            $stmt = $pdo->prepare("SELECT * FROM cms__user WHERE username = ?");
            $stmt->execute([$username]);

            $user = $stmt->fetch();
            if ($user) {
                if (password_verify($password, $user['password'])) {
                    return $response->withRedirect($this->get('base_url'));
                }
            }

            $status = self::HTTP_FAIL;
            $data = [
                'error' => true,
                'username' => $username
            ];
        }

        return $this->get('view')->render($response->withStatus($status), 'login.twig', $data);
    }
}
