<?php

namespace GislerCMS\Controller\Admin\Stats;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Visit;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class VisitsController
 * @package GislerCMS\Controller\Admin
 */
class VisitsController extends AbstractController
{
    const NAME = 'admin-stats-visits';
    const PATTERN = '{admin_route}/stats/visits[/{id:[0-9]+}]';
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

        return $this->render($request, $response, 'admin/stats/visits.twig', [
            'id' => $id
        ]);
    }
}
