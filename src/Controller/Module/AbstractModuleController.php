<?php

namespace GislerCMS\Controller\Module;

use GislerCMS\Controller\Admin\Module\Manage\AbstractController;
use Slim\Http\Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;

/**
 * Class AbstractModuleController
 * @package GislerCMS\Controller\Module
 */
abstract class AbstractModuleController
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var Twig
     */
    protected $view;

    /**
     * @var array
     */
    protected static $exampleConfig = [];

    /**
     * @var string
     */
    protected static $manageController = AbstractController::class;

    /**
     * AbstractModuleController constructor.
     * @param array $config
     * @param Twig $view
     */
    public function __construct(array $config, Twig $view)
    {
        $this->config = $config;
        $this->view = $view;
    }

    /**
     * @param Request $request
     * @return string
     * @throws LoaderError
     */
    public function execute(Request $request): string
    {
        if ($request->isPost()) {
            return $this->onPost($request);
        } else {
            return $this->onGet($request);
        }
    }

    /**
     * Method called on GET-Request
     *
     * @param Request $request
     * @return string
     * @throws LoaderError
     */
    public function onGet(Request $request): string
    {
        return $this->view->fetch('module/not-implemented.twig');
    }

    /**
     * Method called on POST-Request
     *
     * @param Request $request
     * @return string
     * @throws LoaderError
     */
    public function onPost(Request $request): string
    {
        return self::onGet($request);
    }

    /**
     * @return array
     */
    public static function getExampleConfig(): array
    {
        return static::$exampleConfig;
    }

    /**
     * @return string
     */
    public static function getManageController(): string
    {
        return static::$manageController;
    }
}
