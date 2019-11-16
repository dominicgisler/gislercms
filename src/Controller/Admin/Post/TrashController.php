<?php

namespace GislerCMS\Controller\Admin\Post;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Post;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class TrashController
 * @package GislerCMS\Controller\Admin\Post
 */
class TrashController extends AbstractController
{
    const NAME = 'admin-post-trash';
    const PATTERN = '{admin_route}/post/trash';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        if ($request->isPost()) {
            $method = 'restore';
            if (!is_null($request->getParsedBodyParam('delete'))) {
                $method = 'delete';
            }
            $posts = $request->getParsedBodyParam('post');
            foreach ($posts as $key => $val) {
                if ($val) {
                    $post = Post::get($key);
                    if ($method == 'delete') {
                        $post->delete();
                    } else {
                        $post->setTrash(false);
                        $post->save();
                    }
                }
            }
        }

        $posts = Post::getTrash();
        return $this->render($request, $response, 'admin/post/trash.twig', [
            'trashPosts' => $posts
        ]);
    }
}
