<?php

namespace GislerCMS\Model;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class Mailer
 * @package GislerCMS\Model
 */
class Mailer extends PHPMailer
{
    /**
     * Mailer constructor.
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config)
    {
        parent::__construct();

        if ($config['smtp']) {
            $this->isSMTP();
            $this->Host = $config['host'];
            $this->SMTPAuth = $config['smtpauth'];
            $this->Username = $config['username'];
            $this->Password = $config['password'];
            $this->SMTPSecure = $config['smtpsecure'];
            $this->Port = $config['port'];
        }
        $this->CharSet = 'UTF-8';
        $this->setFrom($config['default_email'], $config['default_name']);
    }
}
