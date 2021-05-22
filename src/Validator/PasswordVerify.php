<?php

namespace GislerCMS\Validator;

use GislerCMS\Model\Page;
use Laminas\Validator\AbstractValidator;

/**
 * Class PasswordVerify
 * @package GislerCMS\Validator
 */
class PasswordVerify extends AbstractValidator
{
    private $hash;

    /**
     * StartsWith constructor.
     * @param string $hash
     */
    public function __construct(string $hash)
    {
        $this->hash = $hash;
        parent::__construct($hash);
    }

    /**
     * Returns true if the string does not contain $str
     *
     * @param  Page $value
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->error('wrong input type');
            return false;
        }

        $this->setValue($value);

        if (!password_verify($value, $this->hash)) {
            $this->error('invalid password');
        }

        if ($this->getMessages()) {
            return false;
        }

        return true;
    }
}
