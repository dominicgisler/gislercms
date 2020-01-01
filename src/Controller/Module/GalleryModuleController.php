<?php

namespace GislerCMS\Controller\Module;

use GislerCMS\Controller\Admin\Module\Manage\GalleryController;
use Slim\Http\Request;
use Twig\Error\LoaderError;

/**
 * Class GalleryModuleController
 * @package GislerCMS\Controller\Module
 */
class GalleryModuleController extends AbstractModuleController
{
    /**
     * @var array
     */
    protected static $exampleConfig = [
        'title' => 'Galerie',
        'galleries' => [
            'test01' => [
                'title' => 'Test 1',
                'description' => '',
                'path' => 'gallery/test01',
                'cover' => 'gallery/test01/cover.jpg'
            ],
            'test02' => [
                'title' => 'Test 2',
                'description' => '',
                'path' => 'gallery/test02',
                'cover' => 'gallery/test02/cover.jpg'
            ]
        ]
    ];

    /**
     * @var string
     */
    protected static $manageController = GalleryController::class;

    /**
     * @param Request $request
     * @return string
     * @throws LoaderError
     */
    public function onGet($request)
    {
        $gallery = $request->getAttribute('arguments');

        $page = $request->getAttribute('page');

        $html = '';
        if (!empty($gallery)) {
            if (isset($this->config['galleries'][$gallery])) {
                $html = $this->view->fetch('module/gallery/detail.twig', [
                    'page' => $page,
                    'gallery' => $this->config['galleries'][$gallery]
                ]);
            }
        }

        if (empty($html)) {
            $html = $this->view->fetch('module/gallery/overview.twig', [
                'page' => $page,
                'config' => $this->config
            ]);
        }

        return $html;
    }
}