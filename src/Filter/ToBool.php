<?php

namespace GislerCMS\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class ToBool
 * @package GislerCMS\Filter
 */
class ToBool extends AbstractFilter
{
    /**
     * Returns (bool) $value
     *
     * @param string $value
     * @return bool
     */
    public function filter($value)
    {
        return $value ? true : false;
    }
}
