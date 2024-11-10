<?php

namespace GislerCMS\Controller\Module;

use GislerCMS\Controller\Admin\Module\Manage\CookieLoginController;
use GislerCMS\Helper\SessionHelper;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\Between;
use Laminas\Validator\Date;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\Regex;
use Laminas\Validator\StringLength;
use PHPMailer\PHPMailer\Exception;
use Slim\Http\Request;
use Twig\Error\LoaderError;

/**
 * Class CookieLoginModuleController
 * @package GislerCMS\Controller\Module
 */
class CookieLoginModuleController extends AbstractModuleController
{
    /**
     * @var array
     */
    protected static array $exampleConfig = [
        'cookie' => [
            'name' => 'some_cookie_name',
            'value' => [
                'field' => 'password',
                'map' => [
                    'pass1' => 'cookieval1',
                    'pass2' => 'cookieval2'
                ]
            ],
            'validity_hours' => 24,
            'path' => '',
            'domain' => 'localhost',
            'secure' => true,
            'httponly' => true
        ],
        'form' => [
            'password' => [
                'label' => 'Password',
                'placeholder' => 'Password',
                'type' => 'password',
                'required' => true,
                'validators' => [
                    'in_array' => [
                        'haystack' => [
                            'pass1',
                            'pass2'
                        ]
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
                'label' => 'Login',
                'class' => 'btn-primary'
            ],
            'reset' => [
                'type' => 'reset',
                'label' => 'Abbrechen',
                'class' => 'btn-secondary'
            ]
        ],
        'messages' => [
            'error' => 'Bitte überprüfe deine Eingaben'
        ]
    ];

    /**
     * @var array
     */
    private array $validatorMap = [
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
    protected static string $manageController = CookieLoginController::class;

    /**
     * Render FORM on GET-Request
     *
     * @param Request $request
     * @return string
     * @throws LoaderError
     */
    public function onGet(Request $request): string
    {
        $elems = $this->config['form'];
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
        $elems = $this->config['form'];
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
            $fieldVal = $postData[$this->config['cookie']['value']['field']];
            $cookieVal = $this->config['cookie']['value']['map'][$fieldVal];

            setcookie(
                $this->config['cookie']['name'],
                $cookieVal,
                time() + $this->config['cookie']['validity_hours']*3600,
                $this->config['cookie']['path'],
                $this->config['cookie']['domain'],
                $this->config['cookie']['secure'],
                $this->config['cookie']['httponly']
            );

            header('Location: /' . SessionHelper::getContainer()->offsetGet('last_page'));
            die();
        } else {
            $msg = [
                'class' => 'danger',
                'text' => $this->config['messages']['error']
            ];
        }
        $html .= $this->getForm($elems, $postData, $msg);
        return $html;
    }

    /**
     * @param array $elems
     * @param array $postData
     * @param array $message
     * @return string
     * @throws LoaderError
     */
    private function getForm(array $elems, array $postData = [], array $message = []): string
    {
        return $this->view->fetch('module/cookie-login/form.twig', [
            'recaptcha' => $this->config['recaptcha'],
            'elements' => $elems,
            'data' => $postData,
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