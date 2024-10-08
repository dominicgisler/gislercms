<?php

namespace GislerCMS\Controller\Admin\Api\Stats;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Visit;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class VisitsController
 * @package GislerCMS\Controller\Admin\Api\Stats
 */
class VisitsController extends AbstractController
{
    const NAME = 'admin-api-stats-visits';
    const PATTERN = '{admin_route}/api/stats/visits[/{id:[0-9]+}]';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $id = (int)$request->getAttribute('route')->getArgument('id');

        $stats = [];

        $draw = $request->getQueryParam('draw');
        $start = intval($request->getQueryParam('start'));
        $length = intval($request->getQueryParam('length'));
        $search = $request->getQueryParam('search')['value'];
        $columns = $request->getQueryParam('columns');
        $order = $request->getQueryParam('order')[0];

        $orderCol = $columns[$order['column']]['data'];
        $orderDir = $order['dir'];

        $search = '%' . $search . '%';
        $recordsTotal = Visit::countAll();
        if ($id > 0) {
            $recordsFiltered = Visit::countWhere('`fk_session_id` = ? AND `arguments` LIKE ?', [$id, $search]);
            $visits = Visit::getWhere(sprintf('`fk_session_id` = ? AND `arguments` LIKE ? ORDER BY %s %s LIMIT %d, %d', $orderCol, $orderDir, $start, $length), [$id, $search]);
        } else {
            $recordsFiltered = Visit::countWhere('`arguments` LIKE ?', [$search]);
            $visits = Visit::getWhere(sprintf('`arguments` LIKE ? ORDER BY %s %s LIMIT %d, %d', $orderCol, $orderDir, $start, $length), [$search]);
        }

        $adminURL = $this->get('base_url') . $this->get('settings')['global']['admin_route'];
        foreach ($visits as $visit) {
            $ptrans = $visit->getPageTranslation();
            $stats[] = [
                'visit_id' => $visit->getVisitId(),
                'created_at' => date('d.m.Y H:i:s', strtotime($visit->getCreatedAt())),
                'page_id' => sprintf('<a href="%s/page/%d" class="text-decoration-none">%s</a>', $adminURL, $ptrans->getPage()->getPageId(), $ptrans->getPage()->getName()),
                'language_id' => sprintf('<a href="%s/misc/language/%d" class="text-decoration-none">%s</a>', $adminURL, $ptrans->getLanguage()->getLanguageId(), $ptrans->getLanguage()->getDescription()),
                'arguments' => $visit->getArguments(),
                'redirect_id' => sprintf('<a href="%s/redirect/%d" class="text-decoration-none">%s</a>', $adminURL, $visit->getRedirect()->getRedirectId(), $visit->getRedirect()->getName()),
                'client_id' => sprintf('<a href="%s/stats/sessions/%d" class="text-decoration-none">#%d</a>', $adminURL, $visit->getSession()->getClient()->getClientId(), $visit->getSession()->getClient()->getClientId()),
                'session_id' => sprintf('<a href="%s/stats/visits/%d" class="text-decoration-none">#%d</a>', $adminURL, $visit->getSession()->getSessionId(), $visit->getSession()->getSessionId())
            ];
        }

        return $response->withJson([
            'draw' => $draw,
            'data' => $stats,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ]);
    }
}
