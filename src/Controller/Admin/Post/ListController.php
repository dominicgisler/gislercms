<?php

namespace GislerCMS\Controller\Admin\Post;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Post;
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
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $posts = Post::getAvailable();
        return $this->render($request, $response, 'admin/post/list.twig', [
            'posts' => $posts
        ]);
    }
}
