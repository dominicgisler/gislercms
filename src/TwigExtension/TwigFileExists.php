<?php

namespace GislerCMS\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Custom TwigFileExists twig-extension for using file_exists function in twig
 * @package GislerCMS\TwigExtension
 */
class TwigFileExists extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('file_exists', [$this, 'fileExists'])
        ];
    }

    /**
     * @param string $file
     * @return array|false
     */
    public function fileExists($file)
    {
        return file_exists($file);
    }
}
