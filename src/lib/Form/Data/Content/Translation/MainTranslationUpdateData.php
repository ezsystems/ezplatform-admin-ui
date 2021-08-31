<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation;

use eZ\Publish\API\Repository\Values\Content\Content;
use Symfony\Component\Validator\Constraints as Assert;

class MainTranslationUpdateData
{
    /**
     * @Assert\NotBlank()
     *
     * @var \eZ\Publish\API\Repository\Values\Content\Content|null
     */
    public $content;

    /**
     * @Assert\NotBlank()
     *
     * @var string|null
     */
    public $languageCode;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content|null $content
     * @param string|null $languageCode
     */
    public function __construct(
        ?Content $content = null,
        ?string $languageCode = null
    ) {
        $this->content = $content;
        $this->languageCode = $languageCode;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Content|null
     */
    public function getContent(): ?Content
    {
        return $this->content;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content|null $contentInfo
     */
    public function setContent(?Content $contentInfo = null)
    {
        $this->content = $contentInfo;
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
