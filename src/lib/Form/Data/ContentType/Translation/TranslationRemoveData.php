<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use Symfony\Component\Validator\Constraints as Assert;

class TranslationRemoveData
{
    /**
     * @Assert\NotBlank()
     *
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentType|null
     */
    private $contentType;

    /**
     * @Assert\NotBlank()
     *
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup|null
     */
    private $contentTypeGroup;

    /**
     * @Assert\NotBlank()
     *
     * @var \eZ\Publish\API\Repository\Values\Content\Language[]
     */
    private $languageCodes;

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|null $contentType
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup|null $contentTypeGroup
     * @param array $languageCodes
     */
    public function __construct(
        ContentType $contentType = null,
        ContentTypeGroup $contentTypeGroup = null,
        array $languageCodes = []
    ) {
        $this->contentType = $contentType;
        $this->contentTypeGroup = $contentTypeGroup;
        $this->languageCodes = $languageCodes;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType|null
     */
    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|null $contentType
     *
     * @return self
     */
    public function setContentType(ContentType $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup|null
     */
    public function getContentTypeGroup(): ?ContentTypeGroup
    {
        return $this->contentTypeGroup;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     *
     * @return self
     */
    public function setContentTypeGroup(ContentTypeGroup $contentTypeGroup): self
    {
        $this->contentTypeGroup = $contentTypeGroup;

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language[]
     */
    public function getLanguageCodes(): array
    {
        return $this->languageCodes;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language[] $languageCodes
     */
    public function setLanguageCodes(array $languageCodes): void
    {
        $this->languageCodes = $languageCodes;
    }
}
