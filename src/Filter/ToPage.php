<?php

namespace GislerCMS\Filter;

use Exception;
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
     * @throws Exception
     */
    public function filter($value): Page
    {
        return Page::get(intval($value));
    }
}
