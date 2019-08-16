<?php

namespace GislerCMS\Controller\Admin\Widget;

use GislerCMS\Controller\Admin\AdminAbstractController;
use GislerCMS\Model\Language;
use GislerCMS\Model\Widget;
use GislerCMS\Model\WidgetTranslation;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminWidgetAddController
 * @package GislerCMS\Controller
 */
class AdminWidgetAddController extends AdminAbstractController
{
    const NAME = 'admin-widget-add';
    const PATTERN = '{admin_route}/widget/add';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $langs = Language::getAll();

        $widget = new Widget(0, 'widget_' . microtime(true), false, false, $langs[0]);
        $widget = $widget->save();
        if ($widget instanceof Widget) {
            foreach ($langs as $lang) {
                $widgetTranslation = new WidgetTranslation(0, $widget, $lang, 'Neues Widget');
                $widgetTranslation->save();
            }
        }
        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/widget/' . $widget->getWidgetId());
    }
}
