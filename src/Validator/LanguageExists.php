<?php

namespace GislerCMS\Validator;


use GislerCMS\Model\Language;
use Zend\Validator\AbstractValidator;

class LanguageExists extends AbstractValidator
{
    /**
     * Returns true if the language exists
     *
     * @param  Language $value
     * @return bool
     */
    public function isValid($value)
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