<?php

namespace GislerCMS\Validator;

use GislerCMS\Model\Page;
use Laminas\Validator\AbstractValidator;

/**
 * Class PageExists
 * @package GislerCMS\Validator
 */
class PageExists extends AbstractValidator
{
    /**
     * Returns true if the page exists
     *
     * @param Page $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (!($value instanceof Page)) {
            $this->error('wrong input type');
            return false;
        }

        $this->setValue($value);

        if (!$value->getPageId()) {
            $this->error('page does not exist');
        }

        if ($this->getMessages()) {
            return false;
        }

        return true;
    }
}
