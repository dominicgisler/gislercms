<?php

namespace GislerCMS\Controller\Module;

use GislerCMS\Controller\Admin\Module\Manage\CookieCheckController;
use GislerCMS\Helper\SessionHelper;
use Slim\Http\Request;

/**
 * Class CookieCheckModuleController
 * @package GislerCMS\Controller\Module
 */
class CookieCheckModuleController extends AbstractModuleController
{
    /**
     * @var array
     */
    protected static array $exampleConfig = [
        'name' => 'some_cookie_name',
        'value' => [
            'cookieval1',
            'cookieval2'
        ],
        'redirect' => '/login'
    ];

    /**
     * @var string
     */
    protected static string $manageController = CookieCheckController::class;

    /**
     * @param Request $request
     * @return string
     */
    public function onGet(Request $request): string
    {
        if (!isset($_COOKIE[$this->config['name']]) || !in_array($_COOKIE[$this->config['name']], $this->config['value'])) {
            $lang = $request->getAttribute('route')->getArgument('lang');
            $page = $request->getAttribute('route')->getArgument('page');
            SessionHelper::getContainer()->offsetSet('last_page', ($lang ? $lang . '/' : '') . $page);
            header('Location: ' . $this->config['redirect']);
            die();
        }
        return '';
    }
}