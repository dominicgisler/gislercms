<?php

namespace GislerCMS\Validator;

use GislerCMS\Model\Language;
use Laminas\Validator\AbstractValidator;

/**
 * Class LanguageExists
 * @package GislerCMS\Validator
 */
class LanguageExists extends AbstractValidator
{
    /**
     * Returns true if the language exists
     *
     * @param  Language $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (!($value instanceof Language)) {
            $this->error('wrong input type');
            return false;
        }

        $this->setValue($value);

        if (!$value->getLanguageId()) {
            $this->error('language does not exist');
        }

        if ($this->getMessages()) {
            return false;
        }

        return true;
    }
}
