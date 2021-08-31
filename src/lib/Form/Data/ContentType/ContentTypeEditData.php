<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ContentType;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;

class ContentTypeEditData
{
    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType|null */
    private $contentType;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup|null */
    private $contentTypeGroup;

    /** @var \eZ\Publish\API\Repository\Values\Content\Language|null */
    private $language;

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|null $contentType
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup|null $contentTypeGroup
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $language
     */
    public function __construct(
        ?ContentType $contentType = null,
        ?ContentTypeGroup $contentTypeGroup = null,
        ?Language $language = null
    ) {
        $this->contentType = $contentType;
        $this->contentTypeGroup = $contentTypeGroup;
        $this->language = $language;
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
     */
    public function setContentType(?ContentType $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup|null
     */
    public function getContentTypeGroup(): ?ContentTypeGroup
    {
        return $this->contentTypeGroup;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup|null $contentTypeGroup
     */
    public function setContentTypeGroup(?ContentTypeGroup $contentTypeGroup): void
    {
        $this->contentTypeGroup = $contentTypeGroup;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $language
     */
    public function setLanguage(?Language $language): void
    {
        $this->language = $language;
    }
}
