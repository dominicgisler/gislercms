<?php

namespace GislerCMS\Controller;

use GislerCMS\Filter\ToBool;
use GislerCMS\Filter\ToLanguage;
use GislerCMS\Model\Language;
use GislerCMS\Model\Page;
use GislerCMS\Model\PageTranslation;
use GislerCMS\Validator\LanguageExists;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\InputFilter\Factory;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * Class AdminPageEditController
 * @package GislerCMS\Controller
 */
class AdminPageEditController extends AbstractController
{
    const NAME = 'admin-page-edit';
    const PATTERN = '{admin_route}/page/{id:[0-9]+}';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $id = (int) $request->getAttribute('route')->getArgument('id');
        $page = Page::get($id);
        $languages = Language::getAll();

        if ($page->getPageId() > 0) {
            $translations = PageTranslation::getPageTranslations($page);
            $errors = [];
            if ($request->isPost()) {
                $page = $request->getParsedBodyParam('page');
                $filter = $this->getPageInputFilter();
                $filter->setData($page);
                if (!$filter->isValid()) {
                    $errors = array_merge($errors, array_keys($filter->getMessages()));
                }

                $translations = $request->getParsedBodyParam('translation');
                $filter = $this->getTranslationInputFilter();
                foreach ($translations as $key => $translation) {
                    $filter->setData($translation);
                    if (!$filter->isValid()) {
                        foreach($filter->getMessages() as $field => $msg) {
                            $errors[] = $key . '_' . $field;
                        }
                    }
                }

                if (sizeof($errors) == 0) {
                    // TODO: save
                }
            }
            return $this->render($request, $response, 'admin/page/edit.twig', [
                'page' => $page,
                'languages' => $languages,
                'translations' => $translations,
                'errors' => $errors
            ]);
        }

        return $this->render($request, $response->withStatus(404), 'admin/page/not-found.twig');
    }

    /**
     * @return \Zend\InputFilter\InputFilterInterface
     */
    private function getPageInputFilter()
    {
        $factory = new Factory();
        return $factory->createInputFilter([
            [
                'name' => 'name',
                'required' => true,
                'validators' => [
                    new NotEmpty(),
                    new StringLength([
                        'min' => 1,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'language',
                'required' => true,
                'filters' => [
                    new ToLanguage()
                ],
                'validators' => [
                    new LanguageExists()
                ]
            ],
            [
                'name' => 'enabled',
                'required' => true,
                'filters' => [
                    new ToBool()
                ],
                'validators' => []
            ]
        ]);
    }

    /**
     * @return \Zend\InputFilter\InputFilterInterface
     */
    private function getTranslationInputFilter()
    {
        $factory = new Factory();
        return $factory->createInputFilter([
            [
                'name' => 'enabled',
                'required' => true,
                'filters' => [
                    new ToBool()
                ],
                'validators' => []
            ],
            [
                'name' => 'name',
                'required' => true,
                'validators' => [
                    new NotEmpty(),
                    new StringLength([
                        'min' => 1,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'title',
                'required' => false,
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'meta_keywords',
                'required' => false,
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 512
                    ])
                ]
            ],
            [
                'name' => 'meta_description',
                'required' => false,
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 512
                    ])
                ]
            ],
            [
                'name' => 'meta_author',
                'required' => false,
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 255
                    ])
                ]
            ],
            [
                'name' => 'meta_copyright',
                'required' => false,
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 255
                    ])
                ]
            ],
            [
                'name' => 'meta_image',
                'required' => false,
                'validators' => [
                    new StringLength([
                        'min' => 0,
                        'max' => 255
                    ])
                ]
            ]
        ]);
    }
}
