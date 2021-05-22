<?php

namespace GislerCMS\Filter;

use GislerCMS\Model\Page;
use Laminas\Filter\AbstractFilter;

/**
 * Class ToPage
 * @package GislerCMS\Filter
 */
class ToPage extends AbstractFilter
{
    /**
     * Returns (Page) $value
     *
     * @param string $value
     * @return Page
     * @throws \Exception
     */
    public function filter($value)
    {
        return Page::get(intval($value));
    }
}
