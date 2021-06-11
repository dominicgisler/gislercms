<?php

namespace GislerCMS\Controller\Module;

use Exception;
use GislerCMS\Controller\Admin\Module\Manage\GuestbookController;
use GislerCMS\Model\GuestbookEntry;
use GislerCMS\Model\Mailer;
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
 * Class GuestbookModuleController
 * @package GislerCMS\Controller\Module
 */
class GuestbookModuleController extends AbstractModuleController
{
    /**
     * @var array
     */
    protected static $exampleConfig = [
        'identifier' => 'guestbook',
        'notification' => [
            'enable' => false,
            'mailer' => [
                'smtp' => false,
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
        ],
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
                        'max' => 500
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
            'empty' => 'Bisher keine Einträge',
            'timestamp' => 'am %s um %s'
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
            $entry = new GuestbookEntry();
            $entry->setGuestbookIdentifier($this->config['identifier']);
            $entry->setInput(json_encode($filter->getValues()));

            if ($entry->save()) {
                $postData = [];
                $msg = [
                    'class' => 'success',
                    'text' => $this->config['messages']['success']
                ];
                if (is_array($this->config['notification']) && $this->config['notification']['enable']) {
                    $from = $this->config['notification']['from'];
                    $to = $this->config['notification']['to'];

                    $message = 'Es gibt einen neuen Eintrag im Gästebuch:' . PHP_EOL . PHP_EOL;
                    foreach (json_decode($entry->getInput(), true) as $key => $value) {
                        $message .= $key . ': ' . $value . PHP_EOL;
                    }

                    $mailer = new Mailer($this->config['notification']['mailer']);
                    $mailer->setFrom($from['email'], $from['name']);
                    $mailer->addAddress($to['email'], $to['name']);
                    $mailer->Subject = 'Neuer Eintrag im Gästebuch';
                    $mailer->Body = $message;

                    $mailer->send();
                }
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
     * @throws Exception
     */
    private function getForm(array $elems, array $postData = [], array $errors = [], array $message = []): string
    {
        return $this->view->fetch('module/guestbook/form.twig', [
            'recaptcha' => $this->config['recaptcha'],
            'elements' => $elems,
            'data' => $postData,
            'errors' => $errors,
            'message' => $message,
            'messages' => $this->config['messages'],
            'entries' => GuestbookEntry::getGuestbookEntries($this->config['identifier'])
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