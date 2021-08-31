<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ObjectState;

class ObjectStateGroupsDeleteData
{
    /** @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup[]|null */
    protected $objectStateGroups;

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup[]|null $objectStateGroups
     */
    public function __construct(array $objectStateGroups = [])
    {
        $this->objectStateGroups = $objectStateGroups;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup[]|null
     */
    public function getObjectStateGroups(): ?array
    {
        return $this->objectStateGroups;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup[]|null $objectStateGroups
     */
    public function setObjectStateGroups(?array $objectStateGroups)
    {
        $this->objectStateGroups = $objectStateGroups;
    }
}
