<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Data\Language;

use eZ\Publish\API\Repository\Values\Content\Language;

class LanguageUpdateData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Language */
    private $language;

    /** @var string */
    private $name;

    /** @var bool */
    private $enabled;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $language
     */
    public function __construct(Language $language = null)
    {
        $this->language = $language;
        $this->name = $language->name;
        $this->enabled = $language->enabled;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;
    }

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
