<?php

namespace GislerCMS\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AssetController
 * @package GislerCMS\Controller
 */
class AssetController extends AbstractController
{
    const NAME = 'asset';
    const PATTERN = '/{asset:robots.txt|favicon.ico}';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $asset = $request->getAttribute('route')->getArgument('asset');

        switch ($asset) {
            case 'robots.txt':
                $response = $response->withAddedHeader('Content-Type', 'text/plain');
                break;
            case 'favicon.ico':
                $response = $response->withAddedHeader('Content-Type', 'image/vnd.microsoft.icon');
                break;
        }

        return $this->render($request, $response, sprintf('assets/%s', $asset));
    }
}
