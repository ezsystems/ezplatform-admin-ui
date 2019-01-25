<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo Add validation.
 */
class MainTranslationUpdateData
{
    /**
     * @Assert\NotBlank()
     *
     * @var ContentInfo|null
     */
    public $contentInfo;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $languageCode;

    /**
     * @param ContentInfo|null $contentInfo
     * @param string|null $languageCode
     */
    public function __construct(
        ContentInfo $contentInfo = null,
        string $languageCode = null
    ) {
        $this->contentInfo = $contentInfo;
        $this->languageCode = $languageCode;
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
     * @return string|null
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
}
