<?php

namespace GislerCMS\Controller;

use GislerCMS\Helper\MigrationHelper;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\User;
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
            'error' => false,
            'success' => false,
            'messages' => []
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
                $res = MigrationHelper::executeMigrations($pdo);
                $data['messages'] = $res['migrations'];

                if ($res['status'] === 'error') {
                    $data['error'] = true;
                } else {
                    DbModel::init($pdo);
                    $user = User::create(new User(
                        0,
                        $data['user_username'],
                        $data['user_firstname'],
                        $data['user_lastname'],
                        $data['user_email'],
                        password_hash($data['user_password'], PASSWORD_DEFAULT)
                    ));
                    if ($user->getUserId() === 0) {
                        $data['error'] = true;
                        $data['messages']['create_user'] = [
                            'message' => 'Couldn\'t create admin user!'
                        ];
                    } else {
                        // build config
                        $cfg = "<?php" . PHP_EOL . PHP_EOL .
                            "return [" . PHP_EOL .
                            "    'settings' => [" . PHP_EOL .
                            "        'database' => [" . PHP_EOL .
                            "            'host' => '" . $data['db_host'] . "'," . PHP_EOL .
                            "            'user' => '" . $data['db_user'] . "'," . PHP_EOL .
                            "            'pass' => '" . $data['db_password'] . "'," . PHP_EOL .
                            "            'data' => '" . $data['db_database'] . "'" . PHP_EOL .
                            "        ]," . PHP_EOL . PHP_EOL .
                            "        'enable_setup' => false" . PHP_EOL .
                            "    ]," . PHP_EOL .
                            "];" . PHP_EOL;
                        if (!file_put_contents(__DIR__ . '/../../config/local.php', $cfg)) {
                            $data['error'] = true;
                            $data['messages']['write_config'] = [
                                'message' => 'Could not write config file!'
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                $data['error'] = true;
                $data['messages']['exec_migration'] = [
                    'message' => $e->getMessage()
                ];
            }

            if (empty($data['error'])) {
                $data['success'] = true;
            }
        }

        return $this->render($request, $response, 'setup.twig', $data);
    }
}
