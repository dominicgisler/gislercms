<?php

namespace GislerCMS\Controller\Admin\Widget;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Widget;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ListController
 * @package GislerCMS\Controller\Admin\Widget
 */
class ListController extends AbstractController
{
    const NAME = 'admin-widget-list';
    const PATTERN = '{admin_route}/widget/list';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $widgets = Widget::getAvailable();
        return $this->render($request, $response, 'admin/widget/list.twig', [
            'widgets' => $widgets
        ]);
    }
}
