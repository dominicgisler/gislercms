<?php

namespace GislerCMS\TwigExtension;

use Laminas\I18n\Translator\Loader\Gettext;
use Laminas\I18n\Translator\Translator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Custom TwigTrans twig-extension for translations texts in twig
 * @package GislerCMS\TwigExtension
 */
class TwigTrans extends AbstractExtension
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * TwigTrans constructor.
     */
    public function __construct()
    {
        $this->translator = new Translator();
        $this->translator->addTranslationFilePattern(Gettext::class, __DIR__ . '/../../translations', '%s.mo');
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('trans', [$this, 'trans'])
        ];
    }

    /**
     * @param string $message
     * @return string
     */
    public function trans(string $message): string
    {
        return $this->translator->translate($message);
    }
}
