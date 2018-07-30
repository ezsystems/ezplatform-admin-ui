<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;

/**
 * @todo Add validation. $language have to be validated that $versionInfo indeed has this language
 */
class ContentEditData
{
    /** @var Location|null */
    protected $location;

    /** @var ContentInfo|null */
    protected $contentInfo;

    /** @var VersionInfo|null */
    protected $versionInfo;

    /** @var Language|null */
    protected $language;

    /**
     * @param ContentInfo|null $contentInfo
     * @param VersionInfo|null $versionInfo
     * @param Language|null $language
     * @param Location|null $location
     */
    public function __construct(
        ?ContentInfo $contentInfo = null,
        ?VersionInfo $versionInfo = null,
        ?Language $language = null,
        ?Location $location = null
    ) {
        $this->contentInfo = $contentInfo;
        $this->versionInfo = $versionInfo;
        $this->language = $language;
        $this->location = $location;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param Location|null $location
     */
    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
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
     *
     * @return self
     */
    public function setContentInfo(?ContentInfo $contentInfo): self
    {
        $this->contentInfo = $contentInfo;

        return $this;
    }

    /**
     * @return VersionInfo|null
     */
    public function getVersionInfo(): ?VersionInfo
    {
        return $this->versionInfo;
    }

    /**
     * @param VersionInfo|null $versionInfo
     *
     * @return self
     */
    public function setVersionInfo(?VersionInfo $versionInfo): self
    {
        $this->versionInfo = $versionInfo;

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
     * @param Language|null $language
     *
     * @return self
     */
    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }
}
