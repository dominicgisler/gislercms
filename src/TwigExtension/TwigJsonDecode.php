<?php

namespace GislerCMS\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Custom TwigJsonDecode twig-extension for using json_decode function in twig
 * @package GislerCMS\TwigExtension
 */
class TwigJsonDecode extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('json_decode', [$this, 'jsonDecode'])
        ];
    }

    /**
     * @param string $input
     * @return mixed
     */
    public function jsonDecode($input)
    {
        return json_decode($input);
    }
}
