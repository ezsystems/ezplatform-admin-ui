<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;

class TranslationDeleteData
{
    /** @var ContentInfo|null */
    protected $contentInfo;

    /** @var array|null */
    protected $languageCodes;

    /**
     * @param ContentInfo|null $contentInfo
     * @param array|null $languageCodes
     */
    public function __construct(?ContentInfo $contentInfo = null, array $languageCodes = [])
    {
        $this->contentInfo = $contentInfo;
        $this->languageCodes = $languageCodes;
    }

    /**
     * @return ContentInfo|null
     */
    public function getContentInfo(): ?ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param ContentInfo|null $contentInfo
     */
    public function setContentInfo(?ContentInfo $contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @return array
     */
    public function getLanguageCodes(): array
    {
        return $this->languageCodes;
    }

    /**
     * @param array $languageCodes
     */
    public function setLanguageCodes(array $languageCodes)
    {
        $this->languageCodes = $languageCodes;
    }
}
