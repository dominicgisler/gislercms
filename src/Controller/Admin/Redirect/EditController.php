<?php

namespace GislerCMS\Controller\Admin\Redirect;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Filter\ToBool;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Redirect;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

/**
 * Class EditController
 * @package GislerCMS\Controller\Admin\Redirect
 */
class EditController extends AbstractController
{
    const NAME = 'admin-redirect-edit';
    const PATTERN = '{admin_route}/redirect/{id}';
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
        $cont = SessionHelper::getContainer();

        $msg = false;
        if ($cont->offsetExists('redirect_saved')) {
            $cont->offsetUnset('redirect_saved');
            $msg = 'save_success';
        }

        $id = (int)$request->getAttribute('route')->getArgument('id');
        $redirect = Redirect::get($id);

        $errors = [];
        if ($request->isPost()) {
            if (is_null($request->getParsedBodyParam('delete'))) {
                $data = $request->getParsedBodyParam('redirect');
                $filter = $this->getInputFilter();
                $filter->setData($data);
                if (!$filter->isValid()) {
                    $errors = array_merge($errors, array_keys($filter->getMessages()));
                }
                $widgetData = $filter->getValues();
                $redirect->setName($widgetData['name']);
                $redirect->setEnabled($widgetData['enabled']);
                $redirect->setRoute($widgetData['route']);
                $redirect->setLocation($widgetData['location']);

                if (sizeof($errors) == 0) {
                    $saveError = false;

                    $res = $redirect->save();
                    if (!is_null($res)) {
                        $redirect = $res;
                    } else {
                        $saveError = true;
                    }

                    if ($saveError) {
                        $msg = 'save_error';
                    } else {
                        $cont->offsetSet('redirect_saved', true);
                        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/redirect/' . $redirect->getRedirectId());
                    }
                } else {
                    $msg = 'invalid_input';
                }
            } else {
                $redirect->delete();
                return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route']);
            }
        }

        return $this->render($request, $response, 'admin/redirect/edit.twig', [
            'redirect' => $redirect,
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
                'validators' => [
                    new NotEmpty(),
                    new StringLength([
                        'min' => 1,
                        'max' => 128
                    ])
                ]
            ],
            [
                'name' => 'enabled',
                'required' => false,
                'filters' => [
                    new ToBool()
                ],
                'validators' => []
            ],
            [
                'name' => 'route',
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
                'name' => 'location',
                'required' => true,
                'validators' => [
                    new NotEmpty(),
                    new StringLength([
                        'min' => 1,
                        'max' => 512
                    ])
                ]
            ]
        ]);
    }
}
