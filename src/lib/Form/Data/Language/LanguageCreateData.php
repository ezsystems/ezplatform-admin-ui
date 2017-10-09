<?php

namespace EzSystems\EzPlatformAdminUi\Form\Data\Language;

class LanguageCreateData
{
    /** @var string */
    private $name;

    /** @var string */
    private $languageCode;

    /** @var bool */
    private $enabled;

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    /**
     * @param string $languageCode
     */
    public function setLanguageCode(string $languageCode)
    {
        $this->languageCode = $languageCode;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }
}