<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ObjectState;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;

class ContentObjectStateUpdateData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo|null */
    private $contentInfo;

    /** @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup|null */
    private $objectStateGroup;

    /** @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectState|null rm -r*/
    private $objectState;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

    public function __construct(
        ContentInfo $contentInfo = null,
        ObjectStateGroup $objectStateGroup = null,
        ObjectState $objectState = null,
        Location $location = null
    ) {
        $this->contentInfo = $contentInfo;
        $this->objectStateGroup = $objectStateGroup;
        $this->objectState = $objectState;
        $this->location = $location;
    }

    public function getContentInfo(): ContentInfo
    {
        return $this->contentInfo;
    }

    public function setContentInfo(ContentInfo $contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }

    public function getObjectStateGroup(): ObjectStateGroup
    {
        return $this->objectStateGroup;
    }

    public function setObjectStateGroup(ObjectStateGroup $objectStateGroup)
    {
        $this->objectStateGroup = $objectStateGroup;
    }

    public function getObjectState(): ?ObjectState
    {
        return $this->objectState;
    }

    public function setObjectState(ObjectState $objectState)
    {
        $this->objectState = $objectState;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location)
    {
        $this->location = $location;
    }
}
