<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;

/**
 * @todo Add validation.
 */
class ContentCreateData
{
    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType|null */
    protected $contentType;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    protected $parentLocation;

    /** @var \eZ\Publish\API\Repository\Values\Content\Language|null */
    protected $language;

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|null $contentType
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $parentLocation
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $language
     */
    public function __construct(
        ?ContentType $contentType = null,
        ?Location $parentLocation = null,
        ?Language $language = null
    ) {
        $this->contentType = $contentType;
        $this->parentLocation = $parentLocation;
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
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return self
     */
    public function setContentType(ContentType $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function getParentLocation(): ?Location
    {
        return $this->parentLocation;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     *
     * @return self
     */
    public function setParentLocation(Location $parentLocation): self
    {
        $this->parentLocation = $parentLocation;

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     *
     * @return self
     */
    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }
}
