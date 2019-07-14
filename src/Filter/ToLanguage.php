<?php

namespace GislerCMS\Filter;

use GislerCMS\Model\Language;
use Zend\Filter\AbstractFilter;

class ToLanguage extends AbstractFilter
{
    /**
     * Returns (Language) $value
     *
     * @param string $value
     * @return Language
     * @throws \Exception
     */
    public function filter($value)
    {
        return Language::getLanguage($value);
    }
}
