<?php

namespace GislerCMS\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SetupController
 * @package GislerCMS\Controller
 */
class SetupController extends AbstractController
{
    const NAME = 'setup';
    const PATTERN = '/setup';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke($request, $response)
    {
        if (!$this->get('settings')['enable_setup']) {
            return $response->withRedirect($this->get('base_url'));
        }

        $data = [
            'db_host' => 'localhost',
            'db_database' => '',
            'db_user' => 'root',
            'db_password' => '',
            'user_username' => 'admin',
            'user_firstname' => '',
            'user_lastname' => '',
            'user_email' => '',
            'user_password' => '',
            'error' => '',
            'success' => false,
            'success_msg' => 'Setup successful!'
        ];

        if ($request->isPost()) {
            // pass values from post to $data
            foreach ($request->getParsedBody() as $key => $value) {
                if (isset($data[$key])) {
                    $data[$key] = $value;
                }
            }

            try {
                $pdo = new \PDO(
                    'mysql:host=' . $data['db_host'] . ';dbname=' . $data['db_database'] . ';port=3306',
                    $data['db_user'],
                    $data['db_password']
                );
                $res = $pdo->exec(file_get_contents(__DIR__ . '/../../mysql/01_initial.sql'));

                if ($res === false) {
                    $err = $pdo->errorInfo();
                    if ($err[0] !== '00000' && $err[0] !== '01000') {
                        $data['error'] = 'SQLSTATE[' . $err[0] . '] [' . $err[1] . '] ' . $err[2];
                    }
                }
                if (empty($error)) {
                    $sql = "INSERT INTO `cms__user` (`username`, `firstname`, `lastname`, `email`, `password`) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $res = $stmt->execute([
                        $data['user_username'],
                        $data['user_firstname'],
                        $data['user_lastname'],
                        $data['user_email'],
                        password_hash($data['user_password'], PASSWORD_DEFAULT)
                    ]);
                    if ($res === false) {
                        $err = $stmt->errorInfo();
                        if ($err[0] !== '00000' && $err[0] !== '01000') {
                            $data['error'] = 'SQLSTATE[' . $err[0] . '] [' . $err[1] . '] ' . $err[2];
                        }
                    }
                    if (empty($error)) {
                        // build config
                        $cfg = "<?php" . PHP_EOL . PHP_EOL .
                            "return [" . PHP_EOL .
                            "    'settings' => [" . PHP_EOL .
                            "        'database' => [" . PHP_EOL .
                            "            'host' => '" . $data['db_host'] . "'," . PHP_EOL .
                            "            'user' => '" . $data['db_user'] . "'," . PHP_EOL .
                            "            'pass' => '" . $data['db_password'] . "'," . PHP_EOL .
                            "            'data' => '" . $data['db_database'] . "'" . PHP_EOL .
                            "        ]" . PHP_EOL .
                            "    ]," . PHP_EOL . PHP_EOL .
                            "    'enable_setup' => false" .
                            "];" . PHP_EOL;
                        if (!file_put_contents(__DIR__ . '/../../config/local.php', $cfg)) {
                            $data['error'] = 'Could not write config file!<br>Please check permissions and try again.';
                        }
                    }
                }
            } catch (\Exception $e) {
                $data['error'] = $e->getMessage();
            }

            if (empty($error)) {
                $data['success'] = true;
            }
        }

        return $this->render($request, $response, 'setup.twig', $data);
    }
}
