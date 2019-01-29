<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Symfony\Component\Validator\Constraints as Assert;

class MainTranslationUpdateData
{
    /**
     * @Assert\NotBlank()
     *
     * @var \eZ\Publish\API\Repository\Values\Content\ContentInfo|null
     */
    public $contentInfo;

    /**
     * @Assert\NotBlank()
     *
     * @var string|null
     */
    public $languageCode;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     * @param string|null $languageCode
     */
    public function __construct(
        ?ContentInfo $contentInfo = null,
        ?string $languageCode = null
    ) {
        $this->contentInfo = $contentInfo;
        $this->languageCode = $languageCode;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo|null
     */
    public function getContentInfo(): ?ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     */
    public function setContentInfo(?ContentInfo $contentInfo = null)
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
     * @param string|null $languageCode
     */
    public function setLanguageCode(?string $languageCode = null)
    {
        $this->languageCode = $languageCode;
    }
}
