<?php

namespace GislerCMS\Controller\Admin\Post;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Post;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ListController
 * @package GislerCMS\Controller\Admin\Post
 */
class ListController extends AbstractController
{
    const NAME = 'admin-post-list';
    const PATTERN = '{admin_route}/post/list';
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
        $posts = Post::getAvailable();
        return $this->render($request, $response, 'admin/post/list.twig', [
            'posts' => $posts
        ]);
    }
}
