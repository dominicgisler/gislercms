<?php

namespace GislerCMS\Validator;

use GislerCMS\Model\Page;
use Zend\Validator\AbstractValidator;

/**
 * Class DoesNotContain
 * @package GislerCMS\Validator
 */
class DoesNotContain extends AbstractValidator
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

        if (strpos($value, $this->str) !== false) {
            $this->error('value contains "' . $this->str . '"');
        }

        if ($this->getMessages()) {
            return false;
        }

        return true;
    }
}
