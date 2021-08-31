<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Data\Language;

use eZ\Publish\API\Repository\Values\Content\Language;

class LanguageDeleteData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Language */
    private $language;

    public function __construct(?Language $language = null)
    {
        $this->language = $language;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language
     */
    public function getLanguage(): ?Language
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
}
