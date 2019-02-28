<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;

class ContentData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo */
    private $contentInfo;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location */
    private $location;

    /**
     * ContentData constructor.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     */
    public function __construct(
        ContentInfo $contentInfo = null,
        Location $location = null
    ) {
        $this->contentInfo = $contentInfo;
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
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     */
    public function setContentInfo(ContentInfo $contentInfo): void
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     */
    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }
}
