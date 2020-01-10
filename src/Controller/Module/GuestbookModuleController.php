<?php

namespace GislerCMS\Controller\Module;

use GislerCMS\Controller\Admin\Module\Manage\GuestbookController;
use Slim\Http\Request;
use Twig\Error\LoaderError;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Between;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Validator\InArray;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

/**
 * Class GuestbookModuleController
 * @package GislerCMS\Controller\Module
 */
class GuestbookModuleController extends AbstractModuleController
{
    /**
     * @var array
     */
    protected static $exampleConfig = [
        'elements' => [
            'name' => [
                'label' => 'Name',
                'placeholder' => 'Name',
                'type' => 'text',
                'required' => true,
                'validators' => [
                    'string_length' => [
                        'min' => 3,
                        'max' => 20
                    ]
                ]
            ],
            'message' => [
                'type' => 'textarea',
                'label' => 'Nachricht',
                'placeholder' => 'Nachricht',
                'hint' => 'Ihre Nachricht',
                'required' => true,
                'validators' => [
                    'string_length' => [
                        'min' => 10,
                        'max' => 1000
                    ]
                ]
            ],
            'recaptcha' => [
                'type' => 'recaptcha',
                'website_key' => '',
                'secret_key' => ''
            ],
            'send' => [
                'type' => 'submit',
                'label' => 'Senden',
                'class' => 'btn-primary'
            ],
            'reset' => [
                'type' => 'reset',
                'label' => 'Abbrechen',
                'class' => 'btn-secondary'
            ]
        ],
        'messages' => [
            'error' => 'Bitte überprüfe deine Eingaben',
            'success' => 'Eintrag wurde gespeichert',
            'failed' => 'Es ist ein Fehler aufgetreten, bitte versuche es später erneut',
        ]
    ];

    /**
     * @var array
     */
    private $validatorMap = [
        'not_empty' => NotEmpty::class,
        'string_length' => StringLength::class,
        'email_address' => EmailAddress::class,
        'between' => Between::class,
        'date' => Date::class,
        'in_array' => InArray::class,
        'regex' => Regex::class
    ];

    /**
     * @var string
     */
    protected static $manageController = GuestbookController::class;

    /**
     * Render FORM on GET-Request
     *
     * @param Request $request
     * @return string
     * @throws LoaderError
     */
    public function onGet($request)
    {
        $elems = $this->config['elements'];
        $html = $this->getForm($elems);
        return $html;
    }

    /**
     * Handle POST-Request
     *
     * @param Request $request
     * @return string
     * @throws LoaderError
     */
    public function onPost($request)
    {
        // handle post-request
        $elems = $this->config['elements'];
        $postData = $request->getParsedBody();
        $errors = [];

        $filter = $this->getInputFilter($elems);
        $filter->setData($postData);
        if (!$filter->isValid()) {
            $errors = array_merge($errors, array_keys($filter->getMessages()));
        }
        $postData = $filter->getValues();

        if (isset($elems['recaptcha'])) {
            $recaptchaResponse = $request->getParsedBodyParam('g-recaptcha-response');

            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $options = [
                'http' => [
                    'method' => 'POST',
                    'content' => http_build_query([
                        'secret' => $elems['recaptcha']['secret_key'],
                        'response' => $recaptchaResponse
                    ])
                ]
            ];
            $context = stream_context_create($options);
            $verify = file_get_contents($url, false, $context);
            $checkCaptcha = json_decode($verify);
            if (!$checkCaptcha->success) {
                $errors[] = 'recaptcha';
            }
        }
        $html = '';
        if (empty($errors)) {
            if (true) {
                $postData = [];
                $msg = [
                    'class' => 'success',
                    'text' => $this->config['messages']['success']
                ];
            } else {
                $msg = [
                    'class' => 'danger',
                    'text' => $this->config['messages']['failed']
                ];
            }
        } else {
            $msg = [
                'class' => 'danger',
                'text' => $this->config['messages']['error']
            ];
        }
        $html .= $this->getForm($elems, $postData, $errors, $msg);
        return $html;
    }

    /**
     * @param array $elems
     * @param array $postData
     * @param array $errors
     * @param array $message
     * @return string
     * @throws LoaderError
     */
    private function getForm(array $elems, $postData = [], array $errors = [], array $message = []): string
    {
        return $this->view->fetch('module/guestbook/form.twig', [
            'recaptcha' => $this->config['recaptcha'],
            'elements' => $elems,
            'data' => $postData,
            'errors' => $errors,
            'message' => $message
        ]);
    }

    /**
     * @param array $elems
     * @return InputFilterInterface
     */
    private function getInputFilter(array $elems)
    {
        $spec = [];
        foreach ($elems as $key => $elem) {
            if (in_array($elem['type'], ['divider', 'spacer', 'title', 'submit', 'button', 'reset', 'recaptcha'])) {
                continue;
            }

            $validators = [];
            if (is_array($elem['validators'])) {
                foreach ($elem['validators'] as $name => $opt) {
                    if (array_key_exists($name, $this->validatorMap)) {
                        $validators[] = new $this->validatorMap[$name]($opt);
                    }
                }
            }

            $spec[] = [
                'name' => $key,
                'required' => boolval($elem['required']),
                'validators' => $validators
            ];
        }

        $factory = new Factory();
        return $factory->createInputFilter($spec);
    }
}