<?php

namespace GislerCMS\Controller\Admin\Post;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Filter\ToBool;
use GislerCMS\Filter\ToLanguage;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Language;
use GislerCMS\Model\Module;
use GislerCMS\Model\Post;
use GislerCMS\Model\PostTranslation;
use GislerCMS\Model\User;
use GislerCMS\Model\Widget;
use GislerCMS\Validator\LanguageExists;
use GislerCMS\Validator\ValidDateTime;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\InputFilter\Factory;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * Class EditController
 * @package GislerCMS\Controller\Admin\Post
 */
class EditController extends AbstractController
{
    const NAME = 'admin-post-edit';
    const PATTERN = '{admin_route}/post/{id}';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $cont = SessionHelper::getContainer();
        /** @var User $user */
        $user = $cont->offsetGet('user');

        $id = (int) $request->getAttribute('route')->getArgument('id');
        $post = Post::get($id);
        $languages = Language::getAll();

        $msg = false;

        $cnt = SessionHelper::getContainer();
        if ($cnt->offsetExists('page_saved')) {
            $cnt->offsetUnset('page_saved');
            $msg = 'save_success';
        }

        $translations = PostTranslation::getPostTranslations($post);
        $errors = [];
        if ($request->isPost()) {
            if (is_null($request->getParsedBodyParam('delete'))) {
                $postData = $request->getParsedBodyParam('post');
                $filter = $this->getPostInputFilter();
                $filter->setData($postData);
                if (!$filter->isValid()) {
                    $errors = array_merge($errors, array_keys($filter->getMessages()));
                }
                $postData = $filter->getValues();
                $post->setName($postData['name']);
                $post->setEnabled($postData['enabled']);
                $post->setLanguage($postData['language']);
                $post->setTrash(false);
                $post->setCategories($postData['categories'] ?: []);
                $post->setPublishAt(date('Y-m-d H:i:s', strtotime($postData['publish_at'] ?: 'now')));

                $translationData = $request->getParsedBodyParam('translation');
                $filter = $this->getTranslationInputFilter();
                foreach ($translationData as $key => $translation) {
                    $filter->setData($translation);
                    if (!$filter->isValid()) {
                        foreach ($filter->getMessages() as $field => $msg) {
                            $errors[] = $key . '_' . $field;
                        }
                    }
                    $data = $filter->getValues();
                    if (!isset($translations[$key])) {
                        $translations[$key] = new PostTranslation();
                        $translations[$key]->setLanguage(Language::getLanguage($key));
                        $translations[$key]->setPost($post);
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

                    $res = $post->save();
                    if (!is_null($res)) {
                        $post = $res;
                    } else {
                        $saveError = true;
                    }

                    foreach ($translations as &$translation) {
                        $translation->setPost($post);
                        $res = $translation->save();
                        if (!is_null($res)) {
                            $translation = $res;
                        } else {
                            $saveError = true;
                        }
                    }

                    if ($saveError) {
                        $msg = 'save_error';
                    } else {
                        $cnt->offsetSet('page_saved', true);
                        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/post/' . $post->getPostId());
                    }
                } else {
                    $msg = 'invalid_input';
                }
            } else {
                if ($post->isTrash()) {
                    $post->delete();
                } else {
                    $post->setTrash(true);
                    $post->save();
                }
                return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route']);
            }
        }
        return $this->render($request, $response, 'admin/post/edit.twig', [
            'post' => $post,
            'languages' => $languages,
            'translations' => $translations,
            'errors' => $errors,
            'message' => $msg,
            'widgets' => Widget::getAvailable(),
            'modules' => Module::getAll()
        ]);
    }

    /**
     * @return \Zend\InputFilter\InputFilterInterface
     */
    private function getPostInputFilter()
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
                'name' => 'publish_at',
                'required' => false
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
                'name' => 'categories',
                'required' => true,
                'validators' => [
                    new NotEmpty()
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
     * @return \Zend\InputFilter\InputFilterInterface
     */
    private function getTranslationInputFilter()
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
