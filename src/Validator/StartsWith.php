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
    /**
     * @var string
     */
    private string $str;

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
     * @param Page $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (!is_string($value)) {
            $this->error('wrong input type');
            return false;
        }

        $this->setValue($value);

        if (!str_starts_with($value, $this->str)) {
            $this->error('value does not start with "' . $this->str . '"');
        }

        if ($this->getMessages()) {
            return false;
        }

        return true;
    }
}
