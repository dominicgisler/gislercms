<?php

namespace GislerCMS\Filter;

use GislerCMS\Model\Language;
use Zend\Filter\AbstractFilter;

/**
 * Class ToLanguage
 * @package GislerCMS\Filter
 */
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
        if (!is_string($value)) {
            return new Language();
        }
        return Language::getLanguage($value);
    }
}
