<?php

namespace GislerCMS\Controller\Admin\Misc;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\Page;
use GislerCMS\Model\Post;
use GislerCMS\Model\Widget;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class TrashController
 * @package GislerCMS\Controller\Admin\Misc
 */
class TrashController extends AbstractController
{
    const NAME = 'admin-misc-trash';
    const PATTERN = '{admin_route}/misc/trash';
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
        if ($request->isPost()) {
            $method = 'restore';
            if (!is_null($request->getParsedBodyParam('delete'))) {
                $method = 'delete';
            }
            $pages = $request->getParsedBodyParam('page');
            $posts = $request->getParsedBodyParam('post');
            $widgets = $request->getParsedBodyParam('widget');
            $this->handleElements($pages, $method, Page::class);
            $this->handleElements($posts, $method, Post::class);
            $this->handleElements($widgets, $method, Widget::class);
        }

        $trash = array_merge(
            $this->buildTrashArray(Page::getTrash()),
            $this->buildTrashArray(Post::getTrash()),
            $this->buildTrashArray(Widget::getTrash())
        );
        return $this->render($request, $response, 'admin/misc/trash.twig', [
            'trash' => $trash
        ]);
    }

    /**
     * @param array|null $elems
     * @param string $method
     * @param string $class
     * @return void
     */
    private function handleElements(?array $elems, string $method, string $class): void
    {
        /** @var DbModel $class */
        foreach ($elems as $key => $val) {
            if ($val) {
                $elem = $class::get($key);
                if ($method == 'delete') {
                    $elem->delete();
                } else {
                    $elem->setTrash(false);
                    $elem->save();
                }
            }
        }
    }

    /**
     * @param array $elems
     * @return array
     */
    private function buildTrashArray(array $elems): array
    {
        $arr = [];
        foreach ($elems as $elem) {
            $elArr = [];
            switch (get_class($elem)) {
                case Page::class:
                    $elArr = [
                        'id' => $elem->getPageId(),
                        'type' => 'page'
                    ];
                    break;
                case Post::class:
                    $elArr = [
                        'id' => $elem->getPostId(),
                        'type' => 'post'
                    ];
                    break;
                case Widget::class:
                    $elArr = [
                        'id' => $elem->getWidgetId(),
                        'type' => 'widget'
                    ];
                    break;
            }
            $elArr['name'] = $elem->getName();
            $arr[] = $elArr;
        }
        return $arr;
    }
}
