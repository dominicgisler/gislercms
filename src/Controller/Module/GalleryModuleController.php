<?php

namespace GislerCMS\Controller\Module;

use GislerCMS\Controller\Admin\Module\Manage\GalleryController;
use GislerCMS\Model\PageTranslation;
use Slim\Http\Request;
use Twig\Error\LoaderError;
use ZipArchive;

/**
 * Class GalleryModuleController
 * @package GislerCMS\Controller\Module
 */
class GalleryModuleController extends AbstractModuleController
{
    /**
     * @var array
     */
    protected static array $exampleConfig = [
        'title' => 'Galerie',
        'galleries' => [
            'test01' => [
                'title' => 'Test 1',
                'description' => '',
                'path' => 'gallery/test01',
                'cover' => 'gallery/test01/cover.jpg',
                'download' => true
            ],
            'test02' => [
                'title' => 'Test 2',
                'description' => '',
                'path' => 'gallery/test02',
                'cover' => 'gallery/test02/cover.jpg',
                'download' => false
            ]
        ]
    ];

    /**
     * @var string
     */
    protected static string $manageController = GalleryController::class;

    /**
     * @param Request $request
     * @return string
     * @throws LoaderError
     */
    public function onGet(Request $request): string
    {
        $gallery = $request->getAttribute('arguments');

        /** @var PageTranslation $page */
        $page = $request->getAttribute('page');

        $html = '';
        if (!empty($gallery)) {
            if (isset($this->config['galleries'][$gallery])) {
                $gall = $this->config['galleries'][$gallery];
                $html = $this->view->fetch('module/gallery/detail.twig', [
                    'page' => $page,
                    'gallery' => $gall
                ]);

                $page->setName($page->getName() . '/' . $gallery);
                if (!empty($gall['title'])) {
                    $page->setTitle($gall['title']);
                }
                if (!empty($gall['description'])) {
                    $page->setMetaDescription($gall['description']);
                }
                if (!empty($gall['cover'])) {
                    $page->setMetaImage($gall['cover']);
                }
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

    /**
     * @param Request $request
     * @return string
     * @throws LoaderError
     */
    public function onPost(Request $request): string
    {
        $gallery = $request->getAttribute('arguments');

        if (empty($gallery) || !isset($this->config['galleries'][$gallery]) || !$this->config['galleries'][$gallery]['download']) {
            return $this->onGet($request);
        }

        $filename = tempnam(sys_get_temp_dir(), 'gallery-export');

        $zip = new ZipArchive();
        $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $galPath = realpath(__DIR__ . '/../../../public/uploads/' . $this->config['galleries'][$gallery]['path']);
        foreach (glob($galPath . '/*.*') as $file) {
            $relPath = substr($file, strlen($galPath) + 1);
            $zip->addFile($file, $relPath);
        }

        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename=' . $gallery . '.zip');
        readfile($filename);
        unlink($filename);

        return '';
    }
}