<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
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
    /** @var ContentType|null */
    protected $contentType;

    /** @var Location|null */
    protected $parentLocation;

    /** @var Language|null */
    protected $language;

    /**
     * @param ContentType|null $contentType
     * @param Location|null $parentLocation
     * @param Language|null $language
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
     * @return ContentType|null
     */
    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    /**
     * @param ContentType $contentType
     *
     * @return self
     */
    public function setContentType(ContentType $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return Location|null
     */
    public function getParentLocation(): ?Location
    {
        return $this->parentLocation;
    }

    /**
     * @param Location $parentLocation
     *
     * @return self
     */
    public function setParentLocation(Location $parentLocation): self
    {
        $this->parentLocation = $parentLocation;

        return $this;
    }

    /**
     * @return Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     *
     * @return self
     */
    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }
}
