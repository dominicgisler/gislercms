<?php

namespace GislerCMS\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Custom TwigGlob twig-extension for using glob function in twig
 * @package GislerCMS\TwigExtension
 */
class TwigGlob extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('glob', [$this, 'glob'])
        ];
    }

    /**
     * @param string $pattern
     * @return array|false
     */
    public function glob($pattern)
    {
        return glob($pattern);
    }
}
