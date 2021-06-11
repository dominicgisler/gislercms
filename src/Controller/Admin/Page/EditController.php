<?php

namespace GislerCMS\Controller\Admin\Page;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Filter\ToBool;
use GislerCMS\Filter\ToLanguage;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Config;
use GislerCMS\Model\Language;
use GislerCMS\Model\Module;
use GislerCMS\Model\Page;
use GislerCMS\Model\PageTranslation;
use GislerCMS\Model\PageTranslationHistory;
use GislerCMS\Model\User;
use GislerCMS\Model\Widget;
use GislerCMS\Validator\LanguageExists;
use Slim\Http\Request;
use Slim\Http\Response;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

/**
 * Class EditController
 * @package GislerCMS\Controller\Admin\Page
 */
class EditController extends AbstractController
{
    const NAME = 'admin-page-edit';
    const PATTERN = '{admin_route}/page/{id}';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $cont = SessionHelper::getContainer();
        /** @var User $user */
        $user = $cont->offsetGet('user');

        $msg = false;
        if ($cont->offsetExists('page_saved')) {
            $cont->offsetUnset('page_saved');
            $msg = 'save_success';
        }

        $id = (int) $request->getAttribute('route')->getArgument('id');
        $page = Page::get($id);
        $languages = Language::getAll();

        $defaults = [];
        foreach (Config::getBySection('page') as $config) {
            $defaults[$config->getName()] = $config->getValue();
        }

        $translations = PageTranslation::getPageTranslations($page);
        $translationsHistory = [];
        $errors = [];
        if ($request->isPost()) {
            if (is_null($request->getParsedBodyParam('delete'))) {
                $pageData = $request->getParsedBodyParam('page');
                $filter = $this->getPageInputFilter();
                $filter->setData($pageData);
                if (!$filter->isValid()) {
                    $errors = array_merge($errors, array_keys($filter->getMessages()));
                }
                $pageData = $filter->getValues();
                $page->setName($pageData['name']);
                $page->setEnabled($pageData['enabled']);
                $page->setLanguage($pageData['language']);
                $page->setTrash(false);

                $translationData = $request->getParsedBodyParam('translation');
                $filter = $this->getTranslationInputFilter();
                foreach ($translationData as $key => $translation) {
                    if (count(array_filter($translation)) == 0) {
                        continue; // skip empty translations
                    }
                    $filter->setData($translation);
                    if (!$filter->isValid()) {
                        foreach ($filter->getMessages() as $field => $msg) {
                            $errors[] = $key . '_' . $field;
                        }
                    }
                    $data = $filter->getValues();
                    if (isset($translations[$key])) {
                        if ($translations[$key]->getName() !== $data['name'] ||
                            $translations[$key]->getTitle() !== $data['title'] ||
                            $translations[$key]->getContent() !== $data['content'] ||
                            $translations[$key]->getMetaKeywords() !== $data['meta_keywords'] ||
                            $translations[$key]->getMetaDescription() !== $data['meta_description'] ||
                            $translations[$key]->getMetaAuthor() !== $data['meta_author'] ||
                            $translations[$key]->getMetaCopyright() !== $data['meta_copyright'] ||
                            $translations[$key]->getMetaImage() !== $data['meta_image'] ||
                            $translations[$key]->isEnabled() !== $data['enabled']
                        ) {
                            // create PageTranslationHistory if there are changes
                            $translationsHistory[$key] = new PageTranslationHistory(
                                0,
                                $translations[$key],
                                $translations[$key]->getName(),
                                $translations[$key]->getTitle(),
                                $translations[$key]->getContent(),
                                $translations[$key]->getMetaKeywords(),
                                $translations[$key]->getMetaDescription(),
                                $translations[$key]->getMetaAuthor(),
                                $translations[$key]->getMetaCopyright(),
                                $translations[$key]->getMetaImage(),
                                $translations[$key]->isEnabled(),
                                $user
                            );
                        }
                    } else {
                        $translations[$key] = new PageTranslation();
                        $translations[$key]->setLanguage(Language::getLanguage($key));
                        $translations[$key]->setPage($page);
                    }
                    $translations[$key]->setEnabled($data['enabled']);
                    $translations[$key]->setName($data['name']);
                    $translations[$key]->setTitle($data['title']);
                    $translations[$key]->setMetaKeywords($data['meta_keywords']);
                    $translations[$key]->setMetaDescription($data['meta_description']);
                    $translations[$key]->setMetaAuthor($data['meta_author']);
                    $translations[$key]->setMetaCopyright($data['meta_copyright']);
                    $translations[$key]->setMetaImage($data['meta_image']);
                    $translations[$key]->setContent($data['content']);
                }

                if (sizeof($errors) == 0) {
                    $saveError = false;

                    $res = $page->save();
                    if (!is_null($res)) {
                        $page = $res;
                    } else {
                        $saveError = true;
                    }

                    foreach ($translations as &$translation) {
                        $translation->setPage($page);
                        $res = $translation->save();
                        if (!is_null($res)) {
                            $translation = $res;
                        } else {
                            $saveError = true;
                        }
                    }

                    /** @var PageTranslationHistory $translationHistory */
                    foreach ($translationsHistory as &$translationHistory) {
                        $res = $translationHistory->save();
                        if (!is_null($res)) {
                            $translationHistory = $res;
                        } else {
                            $saveError = true;
                        }
                    }

                    if ($saveError) {
                        $msg = 'save_error';
                    } else {
                        $pid = $page->getPageId();
                        $pname = $page->getName();
                        $ptrans = $page->getPageTranslations();
                        if (!is_null($request->getParsedBodyParam('duplicate'))) {
                            $page->setPageId(0);
                            $page->setName($pname . ' (Copy)');
                            $res = $page->save();
                            if (!is_null($res)) {
                                $pid = $res->getPageId();
                                /** @var PageTranslation $trans */
                                foreach ($ptrans as $trans) {
                                    $trans->setPageTranslationId(0);
                                    $trans->setName($trans->getName() . '-copy');
                                    $trans->setPage($res);
                                    $tres = $trans->save();
                                    if (is_null($tres)) {
                                        $res->delete();
                                        $saveError = true;
                                    }
                                }
                            } else {
                                $saveError = true;
                            }
                        }

                        if ($saveError) {
                            $page->setPageId($pid);
                            $page->setName($pname);
                            $msg = 'save_error';
                        } else {
                            $cont->offsetSet('page_saved', true);
                            return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/page/' . $pid);
                        }
                    }
                } else {
                    $msg = 'invalid_input';
                }
            } else {
                if ($page->isTrash()) {
                    $page->delete();
                } else {
                    $page->setTrash(true);
                    $page->save();
                }
                return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route']);
            }
        }

        $history = [];
        unset($translation);
        foreach ($translations as $translation) {
            $history[$translation->getLanguage()->getLocale()] = PageTranslationHistory::getHistory($translation);
        }

        return $this->render($request, $response, 'admin/page/edit.twig', [
            'page' => $page,
            'languages' => $languages,
            'translations' => $translations,
            'errors' => $errors,
            'message' => $msg,
            'widgets' => Widget::getAvailable(),
            'modules' => Module::getAll(),
            'defaults' => $defaults,
            'history' => $history
        ]);
    }

    /**
     * @return InputFilterInterface
     */
    private function getPageInputFilter(): InputFilterInterface
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
                'required' => false,
                'filters' => [
                    new ToBool()
                ],
                'validators' => []
            ]
        ]);
    }

    /**
     * @return InputFilterInterface
     */
    private function getTranslationInputFilter(): InputFilterInterface
    {
        $factory = new Factory();
        return $factory->createInputFilter([
            [
                'name' => 'enabled',
                'required' => false,
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
            ],
            [
                'name' => 'content',
                'required' => false,
                'validators' => []
            ]
        ]);
    }
}
