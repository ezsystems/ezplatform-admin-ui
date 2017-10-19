<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
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

    public function setName(string $name): LanguageCreateData
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode): LanguageCreateData
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->enabled;
    }

    public function setEnabled(bool $enabled): LanguageCreateData
    {
        $this->enabled = $enabled;

        return $this;
    }
}
