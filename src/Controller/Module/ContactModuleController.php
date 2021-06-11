<?php

namespace GislerCMS\Controller\Module;

use GislerCMS\Controller\Admin\Module\Manage\ContactController;
use GislerCMS\Model\Mailer;
use PHPMailer\PHPMailer\Exception;
use Slim\Http\Request;
use Twig\Error\LoaderError;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\Between;
use Laminas\Validator\Date;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\Regex;
use Laminas\Validator\StringLength;

/**
 * Class ContactModuleController
 * @package GislerCMS\Controller\Module
 */
class ContactModuleController extends AbstractModuleController
{
    /**
     * @var array
     */
    protected static $exampleConfig = [
        'mailer' => [
            'smtp' => true,
            'host' => 'mail.example.com',
            'smtpauth' => true,
            'username' => 'max.muster@example.com',
            'password' => 'mypass',
            'smtpsecure' => 'ssl',
            'port' => 465
        ],
        'from' => [
            'email' => 'max.muster@example.com',
            'name' => 'Max Muster'
        ],
        'to' => [
            'email' => 'max.muster@example.com',
            'name' => 'Max Muster'
        ],
        'subject' => 'Anfrage',
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
            'divider' => [
                'type' => 'divider'
            ],
            'email' => [
                'type' => 'email',
                'label' => 'E-Mail',
                'placeholder' => 'E-Mail',
                'required' => true,
                'validators' => [
                    'string_length' => [
                        'min' => 5,
                        'max' => 50
                    ],
                    'email_address' => true
                ]
            ],
            'title' => [
                'type' => 'title',
                'label' => 'Ein Zwischentitel',
                'class' => 'mb-2'
            ],
            'checkbox1' => [
                'type' => 'checkbox',
                'label' => 'Check 1'
            ],
            'checkbox2' => [
                'type' => 'checkbox',
                'label' => 'Check 2'
            ],
            'radio' => [
                'type' => 'radio',
                'label' => 'RADIO',
                'options' => [
                    'radio1' => 'Auswahl 1',
                    'radio2' => 'Auswahl 2',
                    'radio3' => 'Auswahl 3'
                ]
            ],
            'dropdown' => [
                'type' => 'select',
                'label' => 'Dropdooown',
                'options' => [
                    'drop1' => 'Auswahl 1',
                    'drop2' => 'Auswahl 2',
                    'drop3' => 'Auswahl 3'
                ]
            ],
            'spacer' => [
                'type' => 'spacer'
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
            'success' => 'Nachricht wurde versendet',
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
    protected static $manageController = ContactController::class;

    /**
     * Render FORM on GET-Request
     *
     * @param Request $request
     * @return string
     * @throws LoaderError
     */
    public function onGet(Request $request): string
    {
        $elems = $this->config['elements'];
        return $this->getForm($elems);
    }

    /**
     * Handle POST-Request
     *
     * @param Request $request
     * @return string
     * @throws LoaderError
     * @throws Exception
     */
    public function onPost(Request $request): string
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
            $from = $this->config['from'];
            $to = $this->config['to'];
            $subject = $this->config['subject'];

            $message = $subject . PHP_EOL . PHP_EOL;
            foreach ($postData as $key => $input) {
                if ($elems[$key]) {
                    $label = !empty($elems[$key]['label']) ? $elems[$key]['label'] : $key;
                    $message .= $label . ': ' . $input . PHP_EOL;
                }
            }

            $mailer = new Mailer($this->config['mailer']);
            $mailer->setFrom($from['email'], $from['name']);
            $mailer->addAddress($to['email'], $to['name']);
            $mailer->Subject = $subject;
            $mailer->Body = $message;

            if ($mailer->send()) {
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
    private function getForm(array $elems, array $postData = [], array $errors = [], array $message = []): string
    {
        return $this->view->fetch('module/contact/form.twig', [
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
    private function getInputFilter(array $elems): InputFilterInterface
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
