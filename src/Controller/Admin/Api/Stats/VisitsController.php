<?php

namespace GislerCMS\Controller\Admin\Api\Stats;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Visit;
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
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $id = (int) $request->getAttribute('route')->getArgument('id');
        if ($id > 0) {
            $visits = Visit::getWhere('`fk_session_id` = ?', [$id]);
        } else {
            $visits = Visit::getAll();
        }

        return $response->withJson(['data' => $visits]);
    }
}
