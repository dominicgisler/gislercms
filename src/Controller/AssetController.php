<?php

namespace GislerCMS\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;

/**
 * Class AssetController
 * @package GislerCMS\Controller
 */
class AssetController extends AbstractController
{
    const NAME = 'asset';
    const PATTERN = '/{asset:robots.txt|favicon.ico|assets/.*}';
    const METHODS = ['GET'];

    const HIDDEN_ASSETS = ['robots.txt'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $asset = $request->getAttribute('route')->getArgument('asset');

        if ($asset == 'robots.txt') {
            return $this->render(
                $request,
                $response->withAddedHeader('Content-Type', 'text/plain'),
                sprintf('assets/%s', $asset)
            );
        }

        $asset = str_replace('assets/', '', $asset);
        if (!in_array($asset, self::HIDDEN_ASSETS)) {
            $tplPaths = $this->get('settings')['renderer']['template_paths'];
            foreach ($tplPaths as $path) {
                $file = sprintf('%s/assets/%s', $path, $asset);
                if (file_exists($file)) {
                    $ctype = mime_content_type($file);
                    $str = fopen($file, 'r');
                    return $response
                        ->withHeader('Content-Type', $ctype)
                        ->withBody(new Stream($str));
                }
            }
        }

        return $response->withStatus(404);
    }
}
