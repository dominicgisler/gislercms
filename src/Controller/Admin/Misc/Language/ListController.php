<?php

namespace GislerCMS\Controller\Admin\Misc\Language;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Language;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ListController
 * @package GislerCMS\Controller\Admin\Misc\Language
 */
class ListController extends AbstractController
{
    const NAME = 'admin-misc-language-list';
    const PATTERN = '{admin_route}/misc/language/list';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        return $this->render($request, $response, 'admin/misc/language/list.twig', [
            'languages' => Language::getAll()
        ]);
    }
}
