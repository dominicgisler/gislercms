<?php

namespace GislerCMS\Filter;

use Exception;
use GislerCMS\Model\Language;
use Laminas\Filter\AbstractFilter;

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
     * @throws Exception
     */
    public function filter($value): Language
    {
        if (!is_string($value)) {
            return new Language();
        }
        return Language::getLanguage($value);
    }
}
