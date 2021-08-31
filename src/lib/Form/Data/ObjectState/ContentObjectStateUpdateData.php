<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ObjectState;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;

class ContentObjectStateUpdateData
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    private $contentInfo;

    /**
     * @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup
     */
    private $objectStateGroup;

    /**
     * @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectState
     */
    private $objectState;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup|null $objectStateGroup
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState|null $objectState
     */
    public function __construct(
        ContentInfo $contentInfo = null,
        ObjectStateGroup $objectStateGroup = null,
        ObjectState $objectState = null
    ) {
        $this->contentInfo = $contentInfo;
        $this->objectStateGroup = $objectStateGroup;
        $this->objectState = $objectState;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    public function getContentInfo(): ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     */
    public function setContentInfo(ContentInfo $contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup
     */
    public function getObjectStateGroup(): ObjectStateGroup
    {
        return $this->objectStateGroup;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup
     */
    public function setObjectStateGroup(ObjectStateGroup $objectStateGroup)
    {
        $this->objectStateGroup = $objectStateGroup;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ObjectState\ObjectState|null
     */
    public function getObjectState(): ?ObjectState
    {
        return $this->objectState;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState $objectState
     */
    public function setObjectState(ObjectState $objectState)
    {
        $this->objectState = $objectState;
    }
}
