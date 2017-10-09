<?php

namespace EzSystems\EzPlatformAdminUi\Form\Data\Language;

use eZ\Publish\API\Repository\Values\Content\Language;

class LanguageDeleteData
{
    /** @var Language */
    private $language;

    public function __construct(?Language $language = null)
    {
        $this->language = $language;
    }

    /**
     * @return Language
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;
    }
}