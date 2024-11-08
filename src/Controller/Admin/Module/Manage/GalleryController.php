<?php

namespace GislerCMS\Controller\Admin\Module\Manage;

use Exception;
use GislerCMS\Filter\ToBool;
use GislerCMS\Model\Module;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\IsArray;
use Laminas\Validator\StringLength;
use Slim\Http\Request;

/**
 * Class GalleryController
 * @package GislerCMS\Admin\Module\Manage
 */
class GalleryController extends AbstractController
{
    /**
     * @param Module $mod
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function manage(Module $mod, Request $request): string
    {
        $errors = [];
        $msg = false;

        if ($request->isPost() && is_null($request->getParsedBodyParam('delete'))) {
            $data = $request->getParsedBody();
            $filter = $this->getInputFilter();
            $filter->setData($data);
            if (!$filter->isValid()) {
                $errors = array_merge($errors, array_keys($filter->getMessages()));
            }
            $data = $filter->getValues();

            $galleries = [];
            $filter = $this->getElementInputFilter();
            $i = 0;
            foreach ($data['galleries'] as $gallery) {
                $filter->setData($gallery);
                if (!$filter->isValid()) {
                    foreach ($filter->getMessages() as $key => $msg) {
                        $errors[] = sprintf('galleries[%d][%s]', $i, $key);
                    }
                }
                $gallery = $filter->getValues();
                if (isset($galleries[$gallery['identifier']])) {
                    $gallery['identifier'] .= '_2';
                }
                $galleries[$gallery['identifier']] = $gallery;
                $i++;
            }

            $mod->setName($data['name']);
            $mod->setConfig(json_encode([
                'title' => $data['title'],
                'galleries' => $galleries,
            ]));

            if (sizeof($errors) == 0) {
                $res = $mod->save();
                if (!is_null($res)) {
                    $msg = 'save_success';
                } else {
                    $msg = 'save_error';
                }
            } else {
                $msg = 'invalid_input';
            }
        }

        $config = json_decode($mod->getConfig(), true);

        return $this->view->fetch('admin/module/manage/gallery.twig', [
            'module' => $mod,
            'config' => $config,
            'errors' => $errors,
            'message' => $msg
        ]);
    }

    /**
     * @return InputFilterInterface
     */
    private function getInputFilter(): InputFilterInterface
    {
        $factory = new Factory();
        return $factory->createInputFilter([
            [
                'name' => 'name',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 1,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'title',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 1,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'galleries',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new IsArray()
                ]
            ]
        ]);
    }

    /**
     * @return InputFilterInterface
     */
    private function getElementInputFilter(): InputFilterInterface
    {
        $factory = new Factory();
        return $factory->createInputFilter([
            [
                'name' => 'identifier',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 1,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'title',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 1,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'description',
                'required' => false,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 512
                    ])
                ]
            ],
            [
                'name' => 'path',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 1,
                        'max' => 256
                    ])
                ]
            ],
            [
                'name' => 'cover',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new StringLength([
                        'min' => 1,
                        'max' => 512
                    ])
                ]
            ],
            [
                'name' => 'download',
                'required' => false,
                'filters' => [
                    new ToBool()
                ],
                'validators' => []
            ]
        ]);
    }
}
