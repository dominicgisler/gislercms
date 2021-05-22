<?php

namespace GislerCMS\Validator;

use GislerCMS\Model\Page;
use Laminas\Validator\AbstractValidator;

/**
 * Class StartsWith
 * @package GislerCMS\Validator
 */
class StartsWith extends AbstractValidator
{
    private $str;

    /**
     * StartsWith constructor.
     * @param string $str
     */
    public function __construct(string $str)
    {
        $this->str = $str;
        parent::__construct($str);
    }

    /**
     * Returns true if the string starts with $str
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

        if (!(substr($value, 0, strlen($this->str)) === $this->str)) {
            $this->error('value does not start with "' . $this->str . '"');
        }

        if ($this->getMessages()) {
            return false;
        }

        return true;
    }
}
