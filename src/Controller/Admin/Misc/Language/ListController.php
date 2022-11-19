<?php

namespace GislerCMS\Controller\Admin\Misc\Language;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Language;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->render($request, $response, 'admin/misc/language/list.twig', [
            'languages' => Language::getAll()
        ]);
    }
}
