<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\User;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;

class UserEditData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo|null */
    private $contentInfo;

    /** @var \eZ\Publish\API\Repository\Values\Content\VersionInfo|null */
    private $versionInfo;

    /** @var \eZ\Publish\API\Repository\Values\Content\Language|null */
    private $language;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

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
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo|null
     */
    public function getContentInfo(): ?ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     */
    public function setContentInfo(?ContentInfo $contentInfo): void
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\VersionInfo|null
     */
    public function getVersionInfo(): ?VersionInfo
    {
        return $this->versionInfo;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo|null $versionInfo
     */
    public function setVersionInfo(?VersionInfo $versionInfo): void
    {
        $this->versionInfo = $versionInfo;
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

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     */
    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }
}
