<?php

namespace GislerCMS\Model;

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
     * @throws \PHPMailer\PHPMailer\Exception
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
            $this->setFrom($config['default_from']['email'], $config['default_from']['name']);
        }
    }
}
