<?php

namespace GislerCMS\Controller\Admin\Widget;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Widget;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class TrashController
 * @package GislerCMS\Controller\Admin\Widget
 */
class TrashController extends AbstractController
{
    const NAME = 'admin-widget-trash';
    const PATTERN = '{admin_route}/widget/trash';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        if ($request->isPost()) {
            $method = 'restore';
            if (!is_null($request->getParsedBodyParam('delete'))) {
                $method = 'delete';
            }
            $widgets = $request->getParsedBodyParam('widget');
            foreach ($widgets as $key => $val) {
                if ($val) {
                    $widget = Widget::get($key);
                    if ($method == 'delete') {
                        $widget->delete();
                    } else {
                        $widget->setTrash(false);
                        $widget->save();
                    }
                }
            }
        }

        $widgets = Widget::getTrash();
        return $this->render($request, $response, 'admin/widget/trash.twig', [
            'trashWidgets' => $widgets
        ]);
    }
}
