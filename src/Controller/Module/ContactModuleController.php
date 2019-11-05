<?php

namespace GislerCMS\Controller\Module;

use GislerCMS\Controller\Admin\Module\Manage\ContactController;
use Slim\Http\Request;
use Twig\Error\LoaderError;

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
        'fields' => [
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
            ]
        ],
        'error_message' => 'Bitte überprüfe deine Eingaben',
        'success_message' => 'Nachricht wurde versendet',
        'failed_message' => 'Es ist ein Fehler aufgetreten, bitte versuche es später erneut',
        'buttons' => [
            'send' => [
                'label' => 'Senden',
                'type' => 'submit',
                'class' => 'primary'
            ],
            'reset' => [
                'label' => 'Abbrechen',
                'type' => 'reset',
                'class' => 'secondary'
            ]
        ],
        'recaptcha' => [
            'enable' => false,
            'website_key' => '',
            'secret_key' => ''
        ]
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
     */
    public function onGet($request)
    {
        $fields = $this->config['fields'];
        $buttons = $this->config['buttons'];
        $html = $this->getForm($fields, $buttons);
        return $html;
    }
    /**
     * Handle POST-Request
     *
     * @param Request $request
     * @return string
     */
    public function onPost($request)
    {
        // handle post-request
        $fields = $this->config['fields'];
        $buttons = $this->config['buttons'];
        $postData = $request->getParsedBodyParam('contact');
        $errors = [];
        foreach($postData as $key => $input) {
            $field = $fields[$key];
            if($field['required']) {
                if(isset($field['min_length'])) {
                    if(strlen($input) < $field['min_length']) {
                        $errors[$key] = true;
                    }
                }
                if(isset($field['max_length'])) {
                    if(strlen($input) > $field['max_length']) {
                        $errors[$key] = true;
                    }
                }
                if($field['type'] == 'email') {
                    if(!filter_var($input, FILTER_VALIDATE_EMAIL)) {
                        $errors[$key] = true;
                    }
                }
            }
        }
        if ($this->config['use_recaptcha']) {
            $recaptchaResponse = $request->getParsedBodyParam('g-recaptcha-response');
            $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $this->config['recaptcha_secret_key'] . '&response=' . $recaptchaResponse;
            $checkCaptcha = json_decode(file_get_contents($verifyUrl));
            if (!$checkCaptcha->success) {
                $errors['recaptcha'] = true;
            }
        }
        $html = '';
        if(empty($errors)) {
            $from = $this->config['from'];
            $to = $this->config['to'];
            $subject = $this->config['subject'];
            $message = $subject . PHP_EOL . PHP_EOL;
            foreach($postData as $key => $input) {
                if($fields[$key]) {
                    if($fields[$key]['type'] == 'checkbox') {
                        $message .= $fields[$key]['description'] . PHP_EOL;
                    } else {
                        $message .= $fields[$key]['description'] . ': ' . $input . PHP_EOL;
                    }
                }
            }
//            $mailHelper = new MailHelper();
//            $mail = $mailHelper->getMail();
//            $mail->setFrom($from['email'], $from['name']);
//            $mail->addAddress($to['email'], $to['name']);
//            $mail->Subject = $subject;
//            $mail->Body = $message;
//            if($mail->send()) {
//                $postData = [];
//                $html .= '<strong>' . $this->options['success_message'] . '</strong>';
//            } else {
//                $html .= '<strong>' . $this->options['failed_message'] . '</strong>';
//            }
        } else {
            // show error
            $html .= '<strong>' . $this->config['error_message'] . '</strong>';
        }
        $html .= $this->getForm($fields, $buttons, $postData, $errors);
        return $html;
    }

    /**
     * @param $fields
     * @param $buttons
     * @param array $postData
     * @param array $errors
     * @return string
     * @throws LoaderError
     */
    private function getForm($fields, $buttons, $postData = [], $errors = [])
    {
        return $this->view->fetch('module/contact/form.twig', [
            'fields' => $fields,
            'buttons' => $buttons,
            'data' => $postData,
            'errors' => $errors
        ]);
    }
}