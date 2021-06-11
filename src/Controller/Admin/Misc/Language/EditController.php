<?php

namespace GislerCMS\Controller\Admin\Misc\Language;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Filter\ToBool;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Language;
use Slim\Http\Request;
use Slim\Http\Response;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\StringLength;

/**
 * Class EditController
 * @package GislerCMS\Controller\Admin\Misc\Language
 */
class EditController extends AbstractController
{
    const NAME = 'admin-misc-language-edit';
    const PATTERN = '{admin_route}/misc/language[/{id:[0-9]+}]';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $id = (int) $request->getAttribute('route')->getArgument('id');
        $cont = SessionHelper::getContainer();

        $language = new Language();
        if ($id > 0) {
            $language = Language::get($id);
        }
        $data = $language;

        $errors = [];
        $msg = false;
        if ($cont->offsetExists('language_saved')) {
            $cont->offsetUnset('language_saved');
            $msg = 'save_success';
        }

        if ($request->isPost()) {
            if (is_null($request->getParsedBodyParam('delete'))) {
                $data = $request->getParsedBody();
                $filter = $this->getInputFilter();
                $filter->setData($data);
                if (!$filter->isValid()) {
                    $errors = array_merge($errors, array_keys($filter->getMessages()));
                }
                $data = $filter->getValues();

                if (sizeof($errors) == 0) {
                    $language->setDescription($data['description']);
                    $language->setLocale($data['locale']);
                    $language->setEnabled($data['enabled']);

                    $res = $language->save();
                    if (is_null($res)) {
                        $msg = 'save_error';
                    } else {
                        $cont->offsetSet('language_saved', true);
                        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/misc/language/' . $res->getLanguageId());
                    }
                } else {
                    $msg = 'invalid_input';
                }
            } else {
                if ($language->delete()) {
                    return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route']);
                } else {
                    $msg = 'delete_error';
                }
            }
        }
        return $this->render($request, $response, 'admin/misc/language/edit.twig', [
            'language' => $data,
            'message' => $msg,
            'errors' => $errors
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
                'name' => 'description',
                'required' => true,
                'validators' => [
                    new StringLength([
                        'min' => 1,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'locale',
                'required' => true,
                'validators' => [
                    new StringLength([
                        'min' => 2,
                        'max' => 2
                    ])
                ]
            ],
            [
                'name' => 'enabled',
                'required' => false,
                'filters' => [
                    new ToBool()
                ]
            ]
        ]);
    }
}
