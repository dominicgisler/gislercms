<?php

namespace GislerCMS\Model;

/**
 * Class Language
 * @package GislerCMS\Model
 */
class Language extends DbModel
{
    /**
     * @var int
     */
    private $languageId;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $description;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * Language constructor.
     * @param int $languageId
     * @param string $locale
     * @param string $description
     * @param bool $enabled
     */
    public function __construct(int $languageId = 0, string $locale = '', string $description = '', bool $enabled = true)
    {
        $this->languageId = $languageId;
        $this->locale = $locale;
        $this->description = $description;
        $this->enabled = $enabled;
    }

    /**
     * @param string $locale
     * @return Language
     * @throws \Exception
     */
    public static function getLanguage(string $locale)
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__language` WHERE `locale` = ?");
        $stmt->execute([$locale]);
        $language = $stmt->fetchObject();
        if ($language) {
            return new Language(
                $language->language_id,
                $language->locale,
                $language->description,
                $language->enabled
            );
        }
        return new Language();
    }

    /**
     * @return int
     */
    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    /**
     * @param int $languageId
     */
    public function setLanguageId(int $languageId)
    {
        $this->languageId = $languageId;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }
}
