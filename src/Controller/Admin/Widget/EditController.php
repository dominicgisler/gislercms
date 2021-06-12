<?php

namespace GislerCMS\Controller\Admin\Widget;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Filter\ToBool;
use GislerCMS\Filter\ToLanguage;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Language;
use GislerCMS\Model\Module;
use GislerCMS\Model\Widget;
use GislerCMS\Model\WidgetTranslation;
use GislerCMS\Model\WidgetTranslationHistory;
use GislerCMS\Model\User;
use GislerCMS\Validator\LanguageExists;
use Slim\Http\Request;
use Slim\Http\Response;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

/**
 * Class EditController
 * @package GislerCMS\Controller\Admin\Widget
 */
class EditController extends AbstractController
{
    const NAME = 'admin-widget-edit';
    const PATTERN = '{admin_route}/widget/{id}';
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
        if ($cont->offsetExists('widget_saved')) {
            $cont->offsetUnset('widget_saved');
            $msg = 'save_success';
        }

        $id = (int) $request->getAttribute('route')->getArgument('id');
        $widget = Widget::get($id);
        $languages = Language::getAll();

        $translations = WidgetTranslation::getWidgetTranslations($widget);
        $translationsHistory = [];
        $errors = [];
        if ($request->isPost()) {
            if (is_null($request->getParsedBodyParam('delete'))) {
                $widgetData = $request->getParsedBodyParam('widget');
                $filter = $this->getWidgetInputFilter();
                $filter->setData($widgetData);
                if (!$filter->isValid()) {
                    $errors = array_merge($errors, array_keys($filter->getMessages()));
                }
                $widgetData = $filter->getValues();
                $widget->setName($widgetData['name']);
                $widget->setEnabled($widgetData['enabled']);
                $widget->setLanguage($widgetData['language']);
                $widget->setTrash(false);

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
                    if (isset($translations[$key])) {
                        if ($translations[$key]->getContent() !== $data['content'] ||
                            $translations[$key]->isEnabled() !== $data['enabled']
                        ) {
                            // create WidgetTranslationHistory if there are changes
                            $translationsHistory[$key] = new WidgetTranslationHistory(
                                0,
                                $translations[$key],
                                $translations[$key]->getContent(),
                                $translations[$key]->isEnabled(),
                                $user
                            );
                        }
                    } else {
                        $translations[$key] = new WidgetTranslation();
                        $translations[$key]->setLanguage(Language::getLanguage($key));
                        $translations[$key]->setWidget($widget);
                    }
                    $translations[$key]->setEnabled($data['enabled']);
                    $translations[$key]->setContent($data['content']);
                }

                if (sizeof($errors) == 0) {
                    $saveError = false;

                    $res = $widget->save();
                    if (!is_null($res)) {
                        $widget = $res;
                    } else {
                        $saveError = true;
                    }

                    foreach ($translations as &$translation) {
                        $translation->setWidget($widget);
                        $res = $translation->save();
                        if (!is_null($res)) {
                            $translation = $res;
                        } else {
                            $saveError = true;
                        }
                    }

                    /** @var WidgetTranslationHistory $translationHistory */
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
                        $wid = $widget->getWidgetId();
                        if (!is_null($request->getParsedBodyParam('duplicate'))) {
                            $res = $widget->duplicate();
                            if (!is_null($res)) {
                                $wid = $res->getWidgetId();
                            } else {
                                $saveError = true;
                            }
                        }

                        if ($saveError) {
                            $msg = 'save_error';
                        } else {
                            $cont->offsetSet('widget_saved', true);
                            return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/widget/' . $wid);
                        }
                    }
                } else {
                    $msg = 'invalid_input';
                }
            } else {
                if ($widget->isTrash()) {
                    $widget->delete();
                } else {
                    $widget->setTrash(true);
                    $widget->save();
                }
                return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route']);
            }
        }

        $history = [];
        unset($translation);
        foreach ($translations as $translation) {
            $history[$translation->getLanguage()->getLocale()] = WidgetTranslationHistory::getHistory($translation);
        }

        return $this->render($request, $response, 'admin/widget/edit.twig', [
            'widget' => $widget,
            'languages' => $languages,
            'translations' => $translations,
            'errors' => $errors,
            'message' => $msg,
            'widgets' => Widget::getAvailable(),
            'modules' => Module::getAll(),
            'history' => $history
        ]);
    }

    /**
     * @return InputFilterInterface
     */
    private function getWidgetInputFilter(): InputFilterInterface
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
                'name' => 'content',
                'required' => false,
                'validators' => []
            ]
        ]);
    }
}
