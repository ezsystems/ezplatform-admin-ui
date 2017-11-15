<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ObjectState;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;

class ContentObjectStateUpdateData
{
    /** @var ContentInfo */
    private $contentInfo;

    /** @var ObjectStateGroup */
    private $objectStateGroup;

    /** @var ObjectState */
    private $objectState;

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
     * @return ContentInfo
     */
    public function getContentInfo(): ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param ContentInfo $contentInfo
     */
    public function setContentInfo(ContentInfo $contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @return ObjectStateGroup
     */
    public function getObjectStateGroup(): ObjectStateGroup
    {
        return $this->objectStateGroup;
    }

    /**
     * @param ObjectStateGroup $objectStateGroup
     */
    public function setObjectStateGroup(ObjectStateGroup $objectStateGroup)
    {
        $this->objectStateGroup = $objectStateGroup;
    }

    /**
     * @return ObjectState
     */
    public function getObjectState(): ?ObjectState
    {
        return $this->objectState;
    }

    /**
     * @param ObjectState $objectState
     */
    public function setObjectState(ObjectState $objectState)
    {
        $this->objectState = $objectState;
    }
}
